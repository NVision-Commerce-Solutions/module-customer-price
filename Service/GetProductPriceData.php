<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

class GetProductPriceData
{
    private $priceData;

    public function __construct(
        private readonly GetPriceCollectionForProducts $getPriceCollectionForProduct
    ) {}

    public function execute($productId, $customerId)
    {
        if (!empty($this->priceData[$productId])) {
            return $this->priceData[$productId];
        }
        $this->priceData[$productId] = $this->getPriceCollectionForProduct
            ->execute([$productId], $customerId)
            ->getFirstItem();

        return $this->priceData[$productId];
    }
}
