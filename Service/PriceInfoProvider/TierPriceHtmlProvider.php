<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\View\PriceRenderer;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class TierPriceHtmlProvider implements PriceInfoProviderInterface
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
        if ($product->getTypeId() === Configurable::TYPE_CODE && $product->getId() === $mainProductId) {
            return $this->priceRenderer->renderTierPrice($product);
        }

        if (!$product->getHasTierPrices()) {
            return '';
        }

        if ((int)$product->getId() === (int) $mainProductId) {
            return $this->priceRenderer->renderTierPrice($product);
        }

        if ($this->isChildOfGroupedProduct->check($product, $mainProductId, $mainProductType)) {
            return $this->priceRenderer->renderTierPrice($product);
        }

        return '';
    }
}
