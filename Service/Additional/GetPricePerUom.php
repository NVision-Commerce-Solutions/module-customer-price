<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional;

use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GetPricePerUom
{
    private GetProductPriceData $getProductPriceData;
    private CurrentCustomer $currentCustomer;
    private PriceCurrencyInterface $priceCurrency;
    private PricePerUomChecker $pricePerUomChecker;

    public function __construct(
        GetProductPriceData $getProductPriceData,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        PricePerUomChecker $pricePerUomChecker
    ) {
        $this->getProductPriceData = $getProductPriceData;
        $this->currentCustomer = $currentCustomer;
        $this->priceCurrency = $priceCurrency;
        $this->pricePerUomChecker = $pricePerUomChecker;
    }

    public function execute($productId): array
    {
        if (!$this->pricePerUomChecker->canShow()) {
            return [];
        }

        $customerId = $this->currentCustomer->getId();
        $priceData = $this->getProductPriceData->execute($productId, $customerId);
        $additionalData = $priceData->getAdditionalData();
        if (empty($additionalData['pricePerUOM'])) {
            return [];
        }

        $additionalData['pricePerUOM'] = $this->priceCurrency->convertAndFormat($additionalData['pricePerUOM']);

        return $additionalData;
    }
}
