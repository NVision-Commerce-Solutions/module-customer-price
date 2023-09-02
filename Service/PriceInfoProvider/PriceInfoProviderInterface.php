<?php

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Magento\Catalog\Api\Data\ProductInterface;

interface PriceInfoProviderInterface
{
    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string;
}
