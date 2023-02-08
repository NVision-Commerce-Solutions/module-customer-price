<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class GetProductCollectionWithCustomerPrices
{
    private CollectionFactory $productCollectionFactory;
    private GetPriceCollectionForProducts $getPriceCollectionForProducts;
    private GetProductIdsForRequest $getProductIdsForRequest;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param GetPriceCollectionForProducts $getPriceCollectionForProducts
     * @param GetProductIdsForRequest $getProductIdsForRequest
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        GetPriceCollectionForProducts $getPriceCollectionForProducts,
        GetProductIdsForRequest $getProductIdsForRequest
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->getPriceCollectionForProducts = $getPriceCollectionForProducts;
        $this->getProductIdsForRequest = $getProductIdsForRequest;
    }

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
