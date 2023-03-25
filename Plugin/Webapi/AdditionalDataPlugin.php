<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\Additional\PricePerUomChecker;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class AdditionalDataPlugin
{
    private SessionFactory $customerSessionFactory;
    private GetProductPriceData $getProductPriceData;
    private PriceCurrencyInterface $priceCurrency;
    private PricePerUomChecker $pricePerUomChecker;

    public function __construct(
        SessionFactory $customerSessionFactory,
        GetProductPriceData $getProductPriceData,
        PriceCurrencyInterface $priceCurrency,
        PricePerUomChecker $pricePerUomChecker
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->getProductPriceData = $getProductPriceData;
        $this->priceCurrency = $priceCurrency;
        $this->pricePerUomChecker = $pricePerUomChecker;
    }

    public function afterGetData(Product $subject, $result, $key = null)
    {
        if ($key !== 'additional_price_data') {
            return $result;
        }

        $customerId = $this->customerSessionFactory->create()->getCustomerId();
        if (!$customerId) {
            return $result;
        }

        if (!$this->pricePerUomChecker->canShow()) {
            return $result;
        }

        $priceData = $this->getProductPriceData->execute($subject->getId(), $customerId);
        $additionalData = $priceData->getAdditionalData();
        if (!empty($additionalData['pricePerUOM'])) {
            $additionalData['pricePerUOM'] = $this->priceCurrency->convertAndFormat($additionalData['pricePerUOM']);
        }

        return $additionalData ?? $result;
    }
}
