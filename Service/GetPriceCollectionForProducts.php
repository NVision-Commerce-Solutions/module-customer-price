<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\Core\Service\Customer\GetParentCustomerId;
use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Service\Cache\GetCachedPriceCollection;

class GetPriceCollectionForProducts
{
    public function __construct(
        private readonly GetCachedPriceCollection $getCachedPriceCollection,
        private readonly SyncPrices $syncPrices,
        private readonly PriceCollectionBuilder $priceCollectionBuilder,
        private readonly GetParentCustomerId $getParentCustomerId
    ) {}

    public function execute(array $productIds, $customerId)
    {
        $productIdsToLoad = [];

        $customerId = $this->getParentCustomerId->execute($customerId);
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
