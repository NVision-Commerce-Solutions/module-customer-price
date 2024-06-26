<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Configurable;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Pricing\Price\FinalPriceResolver as MagentoPriceResolver;

class FinalPriceResolver
{
    public function __construct(private readonly MagentoPriceResolver $priceResolver) {}

    /**
     * @param Product $product
     * @return float
     */
    public function execute(Product $product)
    {
        $price = null;
        $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
        foreach ($simpleProducts as $simpleProduct) {
            $productPrice = $this->priceResolver->resolvePrice($simpleProduct);
            $price = isset($price) ? min($price, $productPrice) : $productPrice;
        }

        return (float) $price;
    }
}
