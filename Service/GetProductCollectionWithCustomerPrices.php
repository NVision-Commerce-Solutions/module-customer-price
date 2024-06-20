<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class GetProductCollectionWithCustomerPrices
{
    public function __construct(
        private readonly CollectionFactory $productCollectionFactory,
        private readonly GetPriceCollectionForProducts $getPriceCollectionForProducts,
        private readonly GetProductIdsForRequest $getProductIdsForRequest
    ) {}

    /**
     * @param $storeId
     * @param $productInfo
     * @param $customerId
     * @return Collection
     */
    public function execute($storeId, $productInfo, $customerId): Collection
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addStoreFilter($storeId)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addAttributeToFilter('entity_id', ['in' => $productInfo]);

        if (!$customerId) {
            return $productCollection;
        }

        $productIds = $this->getProductIdsForRequest->execute($productCollection);
        $priceCollection = $this->getPriceCollectionForProducts->execute($productIds, $customerId);
        $this->updateProductCollection($productCollection, $priceCollection);

        return $productCollection;
    }

    private function updateProductCollection($productCollection, $priceCollection): array
    {
        $productIdsToLoad = [];
        foreach ($productCollection as $product) {
            $item = $priceCollection->getItemByColumnValue('productId', $product->getId());
            if (!$item || $item->getPrice() <= 0) {
                continue;
            }
            $product->setPrice($item->getPrice());
            $product->setRegularPrice($item->getPrice());
            $product->setSpecialPrice($item->getSpecialPrice());
            if (!empty($item->getTierPrices())) {
                $product->setHasTierPrices(true);
            }
        }

        return $productIdsToLoad;
    }
}
