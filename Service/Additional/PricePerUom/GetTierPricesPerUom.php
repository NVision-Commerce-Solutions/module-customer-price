<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GetTierPricesPerUom
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
