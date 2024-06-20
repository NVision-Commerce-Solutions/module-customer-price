<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\SetCachedPriceData;
use Commerce365\CustomerPrice\Service\Mapper\ResponseToDatabaseMapperInterface;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPrices;

class SyncPrices
{
    public function __construct(
        private readonly GetCustomerPrices $getCustomerPrices,
        private readonly SetCachedPriceData $setCachedPriceData,
        private readonly ResponseToDatabaseMapperInterface $responseToDatabaseMapper
    ) {}

    public function execute($productIds, $customerId)
    {
        $priceResponse = $this->getCustomerPrices->execute($productIds, $customerId);

        $priceData = $this->responseToDatabaseMapper->map($priceResponse, $customerId);

        $this->setCachedPriceData->execute($priceData);

        return $priceData;
    }
}
