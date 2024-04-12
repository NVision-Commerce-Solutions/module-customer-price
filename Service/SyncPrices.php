<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\SetCachedPriceData;
use Commerce365\CustomerPrice\Service\Mapper\ResponseToDatabaseMapperInterface;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPrices;

class SyncPrices
{
    private SetCachedPriceData $setCachedPriceData;
    private ResponseToDatabaseMapperInterface $responseToDatabaseMapper;
    private GetCustomerPrices $getCustomerPrices;

    public function __construct(
        GetCustomerPrices $getCustomerPrices,
        SetCachedPriceData $setCachedPriceData,
        ResponseToDatabaseMapperInterface $responseToDatabaseMapper
    ) {
        $this->setCachedPriceData = $setCachedPriceData;
        $this->responseToDatabaseMapper = $responseToDatabaseMapper;
        $this->getCustomerPrices = $getCustomerPrices;
    }

    public function execute($productIds, $customerId)
    {
        $priceResponse = $this->getCustomerPrices->execute($productIds, $customerId);

        $priceData = $this->responseToDatabaseMapper->map($priceResponse, $customerId);

        $this->setCachedPriceData->execute($priceData);

        return $priceData;
    }
}
