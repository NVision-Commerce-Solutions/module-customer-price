<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\GetPriceCollectionForProducts;
use Commerce365\CustomerPrice\Service\GetProductIdsForRequest;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Customer\Model\SessionFactory;

class ProductListPricesPreloadToCache
{
    private Config $config;
    private GetProductIdsForRequest $getProductIdsForRequest;
    private GetPriceCollectionForProducts $getPriceCollection;
    private SessionFactory $sessionFactory;

    public function __construct(
        Config $config,
        GetProductIdsForRequest $getProductIdsForRequest,
        GetPriceCollectionForProducts $getPriceCollection,
        SessionFactory $sessionFactory
    ) {
        $this->config = $config;
        $this->getProductIdsForRequest = $getProductIdsForRequest;
        $this->getPriceCollection = $getPriceCollection;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @param ListProduct $subject
     * @param $result
     * @return array
     */
    public function afterGetLoadedProductCollection(ListProduct $subject, $result)
    {
        if (!$this->config->isCachingEnabled()) {
            return $result;
        }

        $session = $this->sessionFactory->create();
        $productIds = $this->getProductIdsForRequest->execute($result);
        $this->getPriceCollection->execute($productIds, $session->getCustomerId());

        return $result;
    }
}
