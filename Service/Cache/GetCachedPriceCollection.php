<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\CustomerPrice\Model\Command\GetCachedPriceData;
use Commerce365\CustomerPrice\Service\PriceCollectionBuilder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;

class GetCachedPriceCollection
{
    private CollectionFactory $collectionFactory;
    private GetCachedPriceData $getCachedPriceData;
    private PriceCollectionBuilder $priceCollectionBuilder;

    /**
     * @param CollectionFactory $collectionFactory
     * @param GetCachedPriceData $getCachedPriceData
     * @param PriceCollectionBuilder $priceCollectionBuilder
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        GetCachedPriceData $getCachedPriceData,
        PriceCollectionBuilder $priceCollectionBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->getCachedPriceData = $getCachedPriceData;
        $this->priceCollectionBuilder = $priceCollectionBuilder;
    }

    public function execute(array $productIds, $customerId): Collection
    {
        $collection = $this->collectionFactory->create();
        if (empty($productIds)) {
            return $collection;
        }

        $queryResult = $this->getCachedPriceData->execute($productIds, $customerId);
        if (empty($queryResult)) {
            return $collection;
        }

        return $this->priceCollectionBuilder->build($queryResult, $customerId);
    }
}
