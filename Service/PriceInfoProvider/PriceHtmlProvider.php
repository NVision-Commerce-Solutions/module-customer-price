<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\View\PriceRenderer;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Pricing\Render;

class PriceHtmlProvider implements PriceInfoProviderInterface
{
    private PriceRenderer $priceRenderer;
    private IsChildOfGroupedProduct $isChildOfGroupedProduct;

    public function __construct(PriceRenderer $priceRenderer, IsChildOfGroupedProduct $isChildOfGroupedProduct)
    {
        $this->priceRenderer = $priceRenderer;
        $this->isChildOfGroupedProduct = $isChildOfGroupedProduct;
    }

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        if ((int) $product->getId() === (int) $mainProductId) {
            return $this->priceRenderer->renderFinalPrice($product, Render::ZONE_ITEM_VIEW, false);
        }

        if ($this->isChildOfGroupedProduct->check($product, $mainProductId, $mainProductType)) {
            return $this->priceRenderer->renderFinalPrice($product, Render::ZONE_ITEM_LIST, false);
        }

        return $this->priceRenderer->renderFinalPrice($product, Render::ZONE_ITEM_LIST);
    }
}
