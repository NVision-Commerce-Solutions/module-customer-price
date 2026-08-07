<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\GetPriceCollectionForProducts;
use Commerce365\CustomerPrice\Service\GetProductIdsForRequest;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Customer\Model\Session;

class ProductListPricesPreloadToCache
{
    public function __construct(
        private readonly Config $config,
        private readonly GetProductIdsForRequest $getProductIdsForRequest,
        private readonly GetPriceCollectionForProducts $getPriceCollection,
        private readonly Session $session
    ) {}

    /**
     * @param ListProduct $subject
     * @param $result
     * @return array
     */
    public function afterGetLoadedProductCollection(ListProduct $subject, $result)
    {
        $session = $this->session;
        if (!$this->config->isCachingEnabled() || !$session->getCustomerId()) {
            return $result;
        }

        $productIds = $this->getProductIdsForRequest->execute($result);
        $this->getPriceCollection->execute($productIds, $session->getCustomerId());

        return $result;
    }
}
