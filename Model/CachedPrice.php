<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model;

use Magento\Framework\DataObject;

class CachedPrice extends DataObject
{
    public const TABLE_NAME = 'commerce365_cached_price';

    private float $price;
    private array $tierPrices;
    private array $additionalData;
    private int $productId;
    private ?float $specialPrice;

    /**
     * @param float $price
     * @param int $productId
     * @param float|null $specialPrice
     * @param array|null $tierPrices
     */
    public function __construct(
        float $price,
        int $productId,
        float $specialPrice = null,
        array $tierPrices = [],
        array $additionalData = []
    ) {
        parent::__construct([
           'price' => $price,
           'tierPrices' => $tierPrices,
           'specialPrice' => $specialPrice,
           'productId' => $productId,
            'additionalData' => $additionalData
        ]);
        $this->price = $price;
        $this->tierPrices = $tierPrices;
        $this->productId = $productId;
        $this->specialPrice = $specialPrice;
        $this->additionalData = $additionalData;
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
