<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GetPricePerUom
{
    public const PRICE_PER_UOM_KEY = 'pricePerUOM';

    public function __construct(
        private readonly GetProductPriceData $getProductPriceData,
        private readonly CurrentCustomer $currentCustomer,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly PricePerUomChecker $pricePerUomChecker
    ) {}

    public function execute($productId): array
    {
        if (!$this->pricePerUomChecker->canShow()) {
            return [];
        }

        $customerId = $this->currentCustomer->getId();
        $priceData = $this->getProductPriceData->execute($productId, $customerId);
        $additionalData = $priceData->getAdditionalData();
        if (empty($additionalData[self::PRICE_PER_UOM_KEY])) {
            return [];
        }

        $additionalData[self::PRICE_PER_UOM_KEY] = $this->priceCurrency->convertAndFormat(
            $additionalData[self::PRICE_PER_UOM_KEY]
        );

        return $additionalData;
    }
}
