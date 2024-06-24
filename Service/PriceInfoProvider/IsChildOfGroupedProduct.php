<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;

class IsChildOfGroupedProduct
{
    public function __construct(private readonly Link $productLinks) {}

    public function check(ProductInterface $product, $mainProductId = null, $mainProductType = ''): bool
    {
        if ($product->getTypeId() !== Type::TYPE_SIMPLE) {
            return false;
        }

        if ($mainProductType !== Grouped::TYPE_CODE) {
            return false;
        }

        $parentIds = $this->productLinks->getParentIdsByChild(
            $product->getId(),
            Link::LINK_TYPE_GROUPED
        );
        if (in_array($mainProductId, $parentIds, true)) {
            return true;
        }

        return false;
    }
}
