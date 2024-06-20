<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model;

use Magento\Framework\DataObject;

class CachedPrice extends DataObject
{
    public const TABLE_NAME = 'commerce365_cached_price';

    public function __construct(
        private readonly float $price,
        private readonly int $productId,
        private readonly ?float $specialPrice = null,
        private readonly array $tierPrices = [],
        private readonly array $additionalData = []
    ) {
        parent::__construct([
            'price' => $price,
            'tierPrices' => $tierPrices,
            'specialPrice' => $specialPrice,
            'productId' => $productId,
            'additionalData' => $additionalData
        ]);
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getTierPrices()
    {
        return $this->tierPrices;
    }

    public function getId()
    {
        return $this->getProductId();
    }

    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    public function getAdditionalData()
    {
        return $this->additionalData;
    }
}
