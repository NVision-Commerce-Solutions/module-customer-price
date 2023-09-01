<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class ProductTypeProvider implements PriceInfoProviderInterface
{
    private IsChildOfGroupedProduct $isChildOfGroupedProduct;

    public function __construct(IsChildOfGroupedProduct $isChildOfGroupedProduct)
    {
        $this->isChildOfGroupedProduct = $isChildOfGroupedProduct;
    }

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        if ($this->isChildOfGroupedProduct->check($product, $mainProductId, $mainProductType)) {
            return 'grouped_child';
        }

        return $product->getTypeId();
    }
}
