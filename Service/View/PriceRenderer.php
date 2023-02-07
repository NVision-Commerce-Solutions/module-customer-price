<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\View;

use Magento\Framework\Pricing\Render;
use Magento\Framework\View\LayoutInterface;

class PriceRenderer
{
    private LayoutInterface $layout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    public function renderFinalPrice($product, $zone = Render::ZONE_ITEM_VIEW, $isItemView = true)
    {
        return $this->getBlock()->render(
            'final_price',
            $product,
            [
                'display_minimal_price'  => $isItemView,
                'use_link_for_as_low_as' => $isItemView,
                'zone' => $zone
            ]
        );
    }

    public function renderTierPrice($product)
    {
        return $this->getBlock()->render('tier_price', $product, ['zone' => 'item_view']);
    }

    private function getBlock()
    {
        $block = $this->layout->getBlock('product.price.render.default');

        if (!$block) {
            $block = $this->layout->createBlock(
                Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $block->setData('cache_lifetime', false);
        return $block;
    }
}
