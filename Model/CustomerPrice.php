<?php

namespace Commerce365\CustomerPrice\Model;

use Commerce365\CustomerPrice\Api\CustomerPriceInterface;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheWrapper;
use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\CurrentStore;
use Commerce365\CustomerPrice\Service\GetProductCollectionWithCustomerPrices;
use Exception;
use Psr\Log\LoggerInterface;

class CustomerPrice implements CustomerPriceInterface
{
    public function __construct(
        private readonly GetProductCollectionWithCustomerPrices $getProductCollectionWithCustomerPrices,
        private readonly HighLevelCacheWrapper $highLevelCacheWrapper,
        private readonly CurrentCustomer $currentCustomer,
        private readonly CurrentStore $currentStore,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @param $productInfo
     * @param $storeId
     * @param $customerId
     * @param $productId
     * @return array
     */
    public function getCustomerPrice(
        $productInfo,
        $storeId,
        $customerId,
        $productId
    ) {
        $response = [];

        if (!$customerId) {
            return $response;
        }
        $this->currentCustomer->setId($customerId);
        $this->currentStore->setId($storeId);

        $productInfo = array_unique($productInfo);
        $productCollection = $this->getProductCollectionWithCustomerPrices->execute($storeId, $productInfo, $customerId);

        try {
            foreach ($productCollection as $product) {
                $response[] = $this->highLevelCacheWrapper->get($product, (int) $productId);

            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $response;
    }
}
