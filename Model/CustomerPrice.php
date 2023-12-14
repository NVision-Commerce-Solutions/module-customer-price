<?php

namespace Commerce365\CustomerPrice\Model;

use Commerce365\CustomerPrice\Api\CustomerPriceInterface;
use Commerce365\CustomerPrice\Model\Command\GetCustomerIdByToken;
use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\GetProductCollectionWithCustomerPrices;
use Commerce365\CustomerPrice\Service\GetProductResponseData;
use Exception;

use Psr\Log\LoggerInterface;

class CustomerPrice implements CustomerPriceInterface
{
    private LoggerInterface $logger;
    private GetProductCollectionWithCustomerPrices $getProductCollectionWithCustomerPrices;
    private GetProductResponseData $getProductResponseData;
    private GetCustomerIdByToken $getCustomerIdByToken;
    private CurrentCustomer $currentCustomer;

    /**
     * @param GetProductCollectionWithCustomerPrices $getProductCollectionWithCustomerPrices
     * @param GetProductResponseData $getProductResponseData
     * @param GetCustomerIdByToken $getCustomerIdByToken
     * @param CurrentCustomer $currentCustomer
     * @param LoggerInterface $logger
     */
    public function __construct(
        GetProductCollectionWithCustomerPrices $getProductCollectionWithCustomerPrices,
        GetProductResponseData $getProductResponseData,
        GetCustomerIdByToken $getCustomerIdByToken,
        CurrentCustomer $currentCustomer,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->getProductCollectionWithCustomerPrices = $getProductCollectionWithCustomerPrices;
        $this->getProductResponseData = $getProductResponseData;
        $this->getCustomerIdByToken = $getCustomerIdByToken;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param $productInfo
     * @param $storeId
     * @param $customerToken
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
        if (empty($productInfo) || !is_array($productInfo) || !$customerId) {
            return $response;
        }
        $this->currentCustomer->setId($customerId);

        $productInfo = array_unique($productInfo);
        $productCollection = $this->getProductCollectionWithCustomerPrices->execute($storeId, $productInfo, $customerId);

        try {
            foreach ($productCollection as $product) {
                $response[] = $this->getProductResponseData->execute($product, $productId);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $response;
    }
}
