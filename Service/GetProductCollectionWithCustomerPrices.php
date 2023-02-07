<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class GetProductCollectionWithCustomerPrices
{
    private CollectionFactory $productCollectionFactory;
    private GetPriceCollectionForProducts $getPriceCollectionForProducts;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param GetPriceCollectionForProducts $getPriceCollectionForProducts
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        GetPriceCollectionForProducts $getPriceCollectionForProducts
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->getPriceCollectionForProducts = $getPriceCollectionForProducts;
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

        $productIds = [];
        foreach ($productCollection as $product) {
            if ($product->getTypeId() === Type::DEFAULT_TYPE) {
                $productIds[] = $product->getId();
            }
        }
        $priceCollection = $this->getPriceCollectionForProducts->execute($productIds, $customerId);
        $this->updateProductCollection($productCollection, $priceCollection);

        return $productCollection;
    }

    private function updateProductCollection($productCollection, $priceCollection): array
    {
        $productIdsToLoad = [];
        foreach ($productCollection as $product) {
            $item = $priceCollection->getItemByColumnValue('productId', $product->getId());
            if (!$item) {
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
