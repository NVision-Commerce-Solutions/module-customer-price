<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi\ConfigurableProduct\Block\Product\View\Type;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetPricePerUom;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as Subject;
use Magento\Framework\Serialize\Serializer\Json;

class AddAdditionalInfo
{
    public function __construct(
        private readonly Json $jsonSerializer,
        private readonly GetPricePerUom $getPricePerUom
    ) {}

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
        if (empty($jsonConfig['optionPrices'])) {
            return $this->getResult($jsonConfig);
        }

        $jsonConfig = $this->addUomPrices($jsonConfig);

        return $this->getResult($jsonConfig);
    }

    private function getResult(array $jsonConfig): bool|string
    {
        return $this->jsonSerializer->serialize($jsonConfig);
    }

    private function addUomPrices(array $jsonConfig): array
    {
        if (empty($jsonConfig['optionPrices'])) {
            return $jsonConfig;
        }

        foreach ($jsonConfig['optionPrices'] as $productId => $optionPrice) {
            $pricePerUom = $this->getPricePerUom->execute($productId);
            if (empty($pricePerUom)) {
                continue;
            }
            $jsonConfig['optionPrices'][$productId]['uomPrice'] = $pricePerUom;
        }

        return $jsonConfig;
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
