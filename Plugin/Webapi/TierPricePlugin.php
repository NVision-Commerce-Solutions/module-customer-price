<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Pricing\Amount\Base;

class TierPricePlugin
{
    private SessionFactory $customerSessionFactory;
    private GetProductPriceData $getProductPriceData;

    public function __construct(
        SessionFactory $customerSessionFactory,
        GetProductPriceData $getProductPriceData
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->getProductPriceData = $getProductPriceData;
    }

    public function afterGetTierPriceList(TierPrice $subject, $result)
    {
        $customerId = $this->customerSessionFactory->create()->getCustomerId();
        if (!$customerId) {
            return $result;
        }

        $product = $subject->getProduct();
        if ($subject->getProduct()->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        $tierPriceList = [];
        $priceId = 0;
        $priceData = $this->getProductPriceData->execute($product->getId(), $customerId);
        $tierPrices = $priceData['tierPrices'] ?? [];

        foreach ($tierPrices as $price) {
            $tierPriceList[] = [
                'price_id' => ++$priceId,
                'website_id' => '0',
                'all_groups' => '1',
                'cust_group' => 0,
                'price' => new Base($price['price']),
                'price_qty' => (int) $price['qty'],
                'website_price' => $price['price'],
            ];
        }

        return $tierPriceList;
    }
}
