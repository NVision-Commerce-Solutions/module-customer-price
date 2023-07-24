<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Service\View\GetConfigurableConfig;
use Commerce365\CustomerPrice\Service\View\GetPriceConfig;
use Commerce365\CustomerPrice\Service\View\PriceRenderer;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\Render;

class GetProductResponseData
{
    private PriceRenderer $priceRenderer;
    private GetPriceConfig $getPriceConfig;
    private GetConfigurableConfig $getConfigurableConfig;

    /**
     * @param PriceRenderer $priceRenderer
     * @param GetPriceConfig $getPriceConfig
     * @param GetConfigurableConfig $getConfigurableConfig
     */
    public function __construct(
        PriceRenderer $priceRenderer,
        GetPriceConfig $getPriceConfig,
        GetConfigurableConfig $getConfigurableConfig
    ) {
        $this->priceRenderer = $priceRenderer;
        $this->getPriceConfig = $getPriceConfig;
        $this->getConfigurableConfig = $getConfigurableConfig;
    }

    public function execute($product, $productId): array
    {
        $blocks = $this->getBlocks($product, $productId);

        return [
            'productId' => $product->getId(),
            'priceHtml' => $blocks['final'],
            'tierPriceHtml' => $blocks['tier'],
            'priceConfig' => $blocks['config'],
            'configurableConfig' => $blocks['configurable']
        ];
    }

    private function getBlocks($product, $productId)
    {
        $tierPriceHtml = $priceConfig = $configurableConfig = '';
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $priceConfig = $this->getPriceConfig->execute($product);
            $configurableConfig = $this->getConfigurableConfig->execute($product);
        }
        if ((int) $product->getId() === (int) $productId) {
            $priceHtml = $this->priceRenderer->renderFinalPrice($product, Render::ZONE_ITEM_VIEW, false);
            $priceConfig = $priceConfig !== '' ? $priceConfig : $this->getPriceConfig->execute($product);
            if ($product->getHasTierPrices()) {
                $tierPriceHtml = $this->priceRenderer->renderTierPrice($product);
            }
        } else {
            $priceHtml = $this->priceRenderer->renderFinalPrice($product, Render::ZONE_ITEM_LIST);
        }

        return [
            'final'=> $priceHtml,
            'tier' => $tierPriceHtml,
            'config' => $priceConfig,
            'configurable' => $configurableConfig
        ];
    }
}
