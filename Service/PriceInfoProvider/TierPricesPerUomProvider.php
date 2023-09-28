<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetTierPricesPerUom;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Serialize\SerializerInterface;

class TierPricesPerUomProvider implements PriceInfoProviderInterface
{
    private GetTierPricesPerUom $getTierPricesPerUom;
    private SerializerInterface $serializer;

    public function __construct(GetTierPricesPerUom $getTierPricesPerUom, SerializerInterface $serializer)
    {
        $this->getTierPricesPerUom = $getTierPricesPerUom;
        $this->serializer = $serializer;
    }

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        $tierPriceData = $this->getTierPricesPerUom->execute($product->getId());

        return $this->serializer->serialize($tierPriceData);
    }
}
