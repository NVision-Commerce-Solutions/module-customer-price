<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\View;

use Commerce365\CustomerPrice\Service\Additional\GetPricePerUom;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;

class GetPriceConfig
{
    private LayoutInterface $layout;
    private GetPricePerUom $getPricePerUom;
    private SerializerInterface $serializer;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout,
        GetPricePerUom $getPricePerUom,
        SerializerInterface $serializer
    ) {
        $this->layout = $layout;
        $this->getPricePerUom = $getPricePerUom;
        $this->serializer = $serializer;
    }

    public function execute(ProductInterface $product)
    {
        $block = $this->getBlock();
        $block->setProductId($product->getId());
        $block->setProductInstance($product);
        $jsonConfig = $block->getJsonConfig();
        $jsonConfig = $this->addUomPrices($product, $this->serializer->unserialize($jsonConfig));

        return $this->serializer->serialize($jsonConfig);
    }

    private function addUomPrices(ProductInterface $product, array $jsonConfig): array
    {
        $pricePerUom = $this->getPricePerUom->execute($product->getId());
        if (empty($pricePerUom)) {
            return $jsonConfig;
        }

        $jsonConfig['uomPrice'] = $pricePerUom;

        return $jsonConfig;
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
