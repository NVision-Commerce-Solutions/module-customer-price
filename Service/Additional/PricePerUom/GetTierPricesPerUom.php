<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GetTierPricesPerUom
{
    public function __construct(
        private readonly GetProductPriceData $getProductPriceData,
        private readonly CurrentCustomer $currentCustomer,
        private readonly PriceCurrencyInterface $priceCurrency,
        private readonly PricePerUomChecker $pricePerUomChecker
    ) {}

    public function execute($productId): array
    {
        $tierPricesPerUom = [];
        if (!$this->pricePerUomChecker->canShow()) {
            return [];
        }

        $customerId = $this->currentCustomer->getId();
        $priceData = $this->getProductPriceData->execute($productId, $customerId);
        $tierPrices = $priceData->getTierPrices();
        if (empty($tierPrices)) {
            return $tierPricesPerUom;
        }

        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['additional'][GetPricePerUom::PRICE_PER_UOM_KEY])) {
                continue;
            }

            $tierPrice['additional'][GetPricePerUom::PRICE_PER_UOM_KEY] = $this->priceCurrency->convertAndFormat(
                $tierPrice['additional'][GetPricePerUom::PRICE_PER_UOM_KEY]
            );
            $tierPricesPerUom[] = $tierPrice;
        }

        return $tierPricesPerUom;
    }
}
