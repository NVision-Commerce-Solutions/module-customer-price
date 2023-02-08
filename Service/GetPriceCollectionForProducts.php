<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Service\Cache\GetCachedPriceCollection;

class GetPriceCollectionForProducts
{
    private GetCachedPriceCollection $getCachedPriceCollection;
    private SyncPrices $syncPrices;
    private PriceCollectionBuilder $priceCollectionBuilder;

    /**
     * @param GetCachedPriceCollection $getCachedPriceCollection
     * @param SyncPrices $syncPrices
     * @param PriceCollectionBuilder $priceCollectionBuilder
     */
    public function __construct(
        GetCachedPriceCollection $getCachedPriceCollection,
        SyncPrices $syncPrices,
        PriceCollectionBuilder $priceCollectionBuilder
    ) {
        $this->getCachedPriceCollection = $getCachedPriceCollection;
        $this->syncPrices = $syncPrices;
        $this->priceCollectionBuilder = $priceCollectionBuilder;
    }

    public function execute(array $productIds, $customerId)
    {
        $productIdsToLoad = [];

        $priceCollection = $this->getCachedPriceCollection->execute($productIds, $customerId);
        /** @var CachedPrice $item */
        foreach ($productIds as $productId) {
            $item = $priceCollection->getItemByColumnValue('productId', $productId);
            if (!$item) {
                $productIdsToLoad[] = $productId;
            }
        }

        if (!empty($productIdsToLoad)) {
            $collection = $this->getCollectionFromERP($productIdsToLoad, $customerId);
            if (!$priceCollection->count()) {
                return $collection;
            }

            if ($collection->count()) {
                foreach ($collection as $priceItem) {
                    $priceCollection->addItem($priceItem);
                }
            }
        }

        return $priceCollection;
    }

    private function getCollectionFromERP($productIdsToLoad, $customerId)
    {
        //Trigger call when cache hours expired for an item or non-existing
        $priceData = $this->syncPrices->execute($productIdsToLoad, $customerId);

        return $this->priceCollectionBuilder->build($priceData, $customerId);
    }
}
