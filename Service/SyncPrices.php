<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\SetCachedPriceData;
use Commerce365\CustomerPrice\Service\Mapper\ResponseToDatabaseMapperInterface;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPrices;

class SyncPrices
{
    private GetCustomerPrices $getCustomerPrices;
    private SetCachedPriceData $setCachedPriceData;
    private ResponseToDatabaseMapperInterface $responseToDatabaseMapper;

    /**
     * @param GetCustomerPrices $getCustomerPrices
     * @param SetCachedPriceData $setCachedPriceData
     * @param ResponseToDatabaseMapperInterface $responseToDatabaseMapper
     */
    public function __construct(
        GetCustomerPrices $getCustomerPrices,
        SetCachedPriceData $setCachedPriceData,
        ResponseToDatabaseMapperInterface $responseToDatabaseMapper
    ) {
        $this->getCustomerPrices = $getCustomerPrices;
        $this->setCachedPriceData = $setCachedPriceData;
        $this->responseToDatabaseMapper = $responseToDatabaseMapper;
    }

    public function execute($productIds, $customerId)
    {
        $priceResponse = $this->getCustomerPrices->execute($productIds, $customerId);

        $priceData = $this->responseToDatabaseMapper->map($priceResponse, $customerId);

        $this->setCachedPriceData->execute($priceData);

        return $priceData;
    }
}
