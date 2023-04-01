<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Framework\Serialize\Serializer\Json;

class AddAdditionalInfo
{
    private Json $jsonSerializer;

    /**
     * @param Json $jsonSerializer
     */
    public function __construct(Json $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Add data about sales channel info and sku.
     *
     * @param Subject $configurable
     * @param string $result
     * @return string
     */
    public function afterGetJsonConfig(Subject $configurable, string $result): string
    {
        $jsonConfig = $this->jsonSerializer->unserialize($result);
        $jsonConfig['sku'] = $this->getProductVariationsSku($configurable);

        return $this->jsonSerializer->serialize($jsonConfig);
    }

    /**
     * Get product variations sku.
     *
     * @param Subject $configurable
     * @return array
     */
    private function getProductVariationsSku(Subject $configurable): array
    {
        $skus = [];
        foreach ($configurable->getAllowProducts() as $product) {
            $skus[$product->getId()] = $product->getSku();
        }

        return $skus;
    }
}
