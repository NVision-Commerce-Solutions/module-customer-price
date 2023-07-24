<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\View;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\View\LayoutInterface;

class GetConfigurableConfig
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
        $block->setProduct($product);
        return $block->getJsonConfig();
    }

    private function getBlock()
    {
        $block = $this->layout->getBlock('product.info.configurable');
        if (!$block) {
            $block = $this->layout->createBlock(
                Configurable::class,
                'product.info.configurable'
            );
        }

        return $block;
    }
}
