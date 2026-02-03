<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class GetProductIdsForRequest
{
    /**
     * @param Collection $collection
     * @return array
     */
    public function execute(Collection $collection): array
    {
        $productIds = [];
        $collectionClone = clone $collection;

        foreach ($collectionClone as $product) {
            $productType = $product->getTypeId();
            if ($productType !== Type::DEFAULT_TYPE && $productType !== Type::TYPE_VIRTUAL) {
                $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId());
                $childrenIds = array_shift($childrenIds);

                //$childrenIds should be always [[]] but in case of an incorrect return type, we add this check
                if (empty($childrenIds)) { continue; }

                foreach ($childrenIds as $childrenId) {
                    $productIds[] = $childrenId;
                }
            } else {
                $productIds[] = $product->getId();
            }
        }

        unset($collectionClone);

        return array_unique($productIds);
    }
}
