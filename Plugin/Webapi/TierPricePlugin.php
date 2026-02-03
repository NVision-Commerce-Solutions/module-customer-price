<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetTierPricesPerUom;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Pricing\Amount\Base;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Tax\Helper\Data;

class TierPricePlugin
{
    public function __construct(
        private readonly SessionFactory $customerSessionFactory,
        private readonly GetProductPriceData $getProductPriceData,
        private readonly GetTierPricesPerUom $getTierPricesPerUom,
        private readonly Config $config,
        private readonly CalculatorInterface $calculator,
        private readonly Data $taxHelper
    ) {}

    public function afterGetTierPriceList(TierPrice $subject, $result)
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return $result;
        }

        $product = $subject->getProduct();
        if ($product->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        $tierPriceList = [];
        $priceId = 0;
        $priceData = $this->getProductPriceData->execute($product->getId(), $customerId);
        $tierPrices = $priceData['tierPrices'] ?? [];
        $displayInclTax = $this->taxHelper->displayPriceIncludingTax();

        foreach ($tierPrices as $tierPrice) {
            $price = $displayInclTax ?
                new Base($tierPrice['price']) : $this->calculator->getAmount($tierPrice['price'], $product);
            $tierPriceData = [
                'price_id' => ++$priceId,
                'website_id' => '0',
                'all_groups' => '1',
                'cust_group' => 0,
                'price' => $price,
                'price_qty' => (int) $tierPrice['qty'],
                'website_price' => $tierPrice['price'],
            ];

            $tierPriceList[] = $tierPriceData;
        }

        if ($this->config->showPricePerUomTier()) {
            $tierPriceList = $this->addTierPricesPerUom($product, $tierPriceList);
        }

        return $tierPriceList;
    }

    private function addTierPricesPerUom(Product $product, array $tierPriceList): array
    {
        $uomTierPrices = $this->getTierPricesPerUom->execute($product->getId());
        foreach ($tierPriceList as $key => $tierPrice) {
            foreach ($uomTierPrices as $uomTierPrice) {
                if ($uomTierPrice['qty'] === $tierPrice['price_qty']) {
                    $tierPriceList[$key]['uom_price_data'] = $uomTierPrice['additional'];
                }
            }
        }

        return $tierPriceList;
    }
}
