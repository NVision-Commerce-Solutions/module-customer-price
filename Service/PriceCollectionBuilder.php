<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\CachedPriceFactory;
use Commerce365\CustomerPrice\Service\Response\PostProcessorInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Serialize\SerializerInterface;

class PriceCollectionBuilder
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CachedPriceFactory $cachedPriceFactory,
        private readonly SerializerInterface $serializer,
        private readonly PostProcessorInterface $postProcessor
    ) {}

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
            return $this->simulateData($customerId, $collection);
        }

        foreach ($buildData as $item) {
            $priceData = $this->serializer->unserialize($item['price_data']);
            $priceData = $this->postProcessor->process($priceData, $item['product_id']);
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

    private function simulateData($customerId, $collection)
    {
        $cachedPrice = $this->cachedPriceFactory->create([
            'price' => 0,
            'productId' => 0,
            'specialPrice' => null,
            'customerId' => $customerId,
            'tierPrices' => [],
            'additionalData' => []
        ]);

        return $collection->addItem($cachedPrice);
    }
}
