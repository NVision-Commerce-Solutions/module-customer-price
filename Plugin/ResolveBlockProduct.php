<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;

class ResolveBlockProduct
{
    /**
     * @param View $subject
     * @param $result
     * @return Product
     */
    public function afterGetProduct(View $subject, $result): Product
    {
        return $subject->getProductInstance();
    }
}
