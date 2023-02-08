<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

class GetProductPriceData
{
    private $priceData;
    private GetPriceCollectionForProducts $getPriceCollectionForProduct;

    /**
     * @param GetPriceCollectionForProducts $getPriceCollectionForProduct
     */
    public function __construct(
        GetPriceCollectionForProducts $getPriceCollectionForProduct
    ) {
        $this->getPriceCollectionForProduct = $getPriceCollectionForProduct;
    }

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
