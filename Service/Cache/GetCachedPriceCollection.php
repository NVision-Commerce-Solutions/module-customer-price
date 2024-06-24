<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\CustomerPrice\Model\Command\GetCachedPriceData;
use Commerce365\CustomerPrice\Service\PriceCollectionBuilder;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\CollectionFactory;

class GetCachedPriceCollection
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly GetCachedPriceData $getCachedPriceData,
        private readonly PriceCollectionBuilder $priceCollectionBuilder
    ) {}

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
