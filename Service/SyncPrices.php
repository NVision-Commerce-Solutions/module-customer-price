<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\SetCachedPriceData;
use Commerce365\CustomerPrice\Service\Mapper\ResponseToDatabaseMapperInterface;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPricesFactory;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPricesInterface;
use Magento\Framework\Exception\RuntimeException;

class SyncPrices
{
    private GetCustomerPricesFactory $getCustomerPricesFactory;
    private SetCachedPriceData $setCachedPriceData;
    private ResponseToDatabaseMapperInterface $responseToDatabaseMapper;

    public function __construct(
        GetCustomerPricesFactory $getCustomerPricesFactory,
        SetCachedPriceData $setCachedPriceData,
        ResponseToDatabaseMapperInterface $responseToDatabaseMapper
    ) {
        $this->getCustomerPricesFactory = $getCustomerPricesFactory;
        $this->setCachedPriceData = $setCachedPriceData;
        $this->responseToDatabaseMapper = $responseToDatabaseMapper;
    }

    public function execute($productIds, $customerId)
    {
        $getCustomerPrices = $this->getCustomerPricesFactory->create();
        if (!$getCustomerPrices instanceof GetCustomerPricesInterface) {
            throw new RuntimeException(
                __("Class %1 should implements GetCustomerPricesInterface", get_class($getCustomerPrices))
            );
        }

        $priceResponse = $getCustomerPrices->execute($productIds, $customerId);

        $priceData = $this->responseToDatabaseMapper->map($priceResponse, $customerId);

        $this->setCachedPriceData->execute($priceData);

        return $priceData;
    }
}
