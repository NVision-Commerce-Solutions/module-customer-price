<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\GetProductResponseData;

class HighLevelCacheWrapper
{
    public function __construct(
        private readonly GetProductResponseData $getProductResponseData,
        private readonly HighLevelCacheManager $highLevelCacheManager,
        private readonly Config $config
    ) {}

    public function get($product, int $productId)
    {
        if (!$this->isEnabled($productId)) {
            return $this->getProductResponseData->execute($product, $productId);
        }

        $priceData = $this->highLevelCacheManager->get($product, $productId);
        if (!empty($priceData)) {
            return $priceData;
        }
        $priceData = $this->getProductResponseData->execute($product, $productId);
        $this->highLevelCacheManager->set($product, $productId, $priceData);

        return $priceData;
    }

    private function isEnabled($productId): bool
    {
        return $productId === 0 && $this->config->isHighLevelCachingEnabled();
    }
}
