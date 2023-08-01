<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\View;

use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\LayoutInterface;

class GetPriceConfig
{
    private LayoutInterface $layout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    public function execute(Product $product)
    {
        $block = $this->getBlock();
        $block->setProductId($product->getId());
        $block->setProductInstance($product);
        return $block->getJsonConfig();
    }

    private function getBlock()
    {
        $block = $this->layout->getBlock('product.info');
        if (!$block) {
            $block = $this->layout->createBlock(
                View::class,
                'product.info'
            );
        }

        return $block;
    }
}
