<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Magento\Catalog\Api\Data\ProductInterface;

class ProductTypeProvider implements PriceInfoProviderInterface
{
    public function __construct(private readonly IsChildOfGroupedProduct $isChildOfGroupedProduct) {}

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        if ($this->isChildOfGroupedProduct->check($product, $mainProductId, $mainProductType)) {
            return 'grouped_child';
        }

        return $product->getTypeId();
    }
}
