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
        foreach ($collection as $product) {
            if ($product->getTypeId() !== Type::DEFAULT_TYPE) {
                $childrenIds = $product->getTypeInstance()->getChildrenIds($product->getId());
                $childrenIds = array_shift($childrenIds);
                foreach ($childrenIds as $childrenId) {
                    $productIds[] = $childrenId;
                }
            } else {
                $productIds[] = $product->getId();
            }
        }

        return array_unique($productIds);
    }
}
