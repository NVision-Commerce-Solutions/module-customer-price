<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\CachedPriceFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

class PriceCollectionBuilder
{
    private CollectionFactory $collectionFactory;
    private CachedPriceFactory $cachedPriceFactory;
    private SerializerInterface $serializer;

    /**
     * @param CollectionFactory $collectionFactory
     * @param CachedPriceFactory $cachedPriceFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CachedPriceFactory $cachedPriceFactory,
        SerializerInterface $serializer
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->cachedPriceFactory = $cachedPriceFactory;
        $this->serializer = $serializer;
    }

    /**
     * @param array $buildData
     * @param $customerId
     * @return Collection
     * @throws \Exception
     */
    public function build(array $buildData, $customerId): Collection
    {
        $buildData = array_filter($buildData);

        $collection = $this->collectionFactory->create();

        if (empty($buildData)) {
            return $collection;
        }

        foreach ($buildData as $item) {
            $priceData = $this->serializer->unserialize($item['price_data']);
            $cachedPrice = $this->cachedPriceFactory->create([
                'price' => $priceData['price'] ?? null,
                'productId' => $item['product_id'],
                'specialPrice' => $priceData['special_price'] ?? null,
                'customerId' => $customerId,
                'tierPrices' => $priceData['tier_prices'] ?? [],
                'additionalData' => $priceData['additional'] ?? []
            ]);
            $collection->addItem($cachedPrice);
        }

        return $collection;
    }
}
