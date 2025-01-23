<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetTierPricesPerUom;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;

class TierPricesPerUomProvider implements PriceInfoProviderInterface
{
    public function __construct(
        private readonly GetTierPricesPerUom $getTierPricesPerUom,
        private readonly SerializerInterface $serializer
    ) {}

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        $tierPriceData = [];
        if ((int) $mainProductId !== (int) $product->getId()) {
            return $this->serializer->serialize($tierPriceData);
        }

        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $tierPriceData = $this->getConfigurablePriceData($product);
        } else {
            $tierPriceData[$product->getId()] = $this->getTierPricesPerUom->execute($product->getId());
        }

        return $this->serializer->serialize($tierPriceData);
    }

    private function getConfigurablePriceData(ProductInterface $product): array
    {
        $tierPriceData = [];
        foreach ($product->getTypeInstance()->getUsedProducts($product) as $child) {
            $tierPricePerUOM = $this->getTierPricesPerUom->execute($child->getId());
            if (empty($tierPricePerUOM)) {
                continue;
            }

            $tierPriceData[$child->getId()] = $tierPricePerUOM;
        }

        return $tierPriceData;
    }
}
