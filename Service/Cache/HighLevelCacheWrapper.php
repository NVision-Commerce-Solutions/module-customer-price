<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\CustomerPrice\Model\Command\GetCachedPriceData;
use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductResponseData;

class HighLevelCacheWrapper
{
    public function __construct(
        private readonly GetProductResponseData $getProductResponseData,
        private readonly CurrentCustomer $currentCustomer,
        private readonly  GetCachedPriceData $getCachedPriceData,
        private readonly HighLevelCacheManager $highLevelCacheManager,
        private readonly Config $config
    ) {}

    public function get($product, int $productId)
    {
        if (!$this->isEnabled($product, $productId)) {
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

    private function isEnabled($product, $productId): bool
    {
        if ($productId !== 0 || !$this->config->isHighLevelCachingEnabled()) {
            return false;
        }

        $cacheExists = $this->getCachedPriceData->execute([$product->getId()], $this->currentCustomer->getId());

        return !empty($cacheExists);
    }
}
