<?php

namespace Commerce365\CustomerPrice\Service\Mapper;

interface ResponseToDatabaseMapperInterface
{
    public function map(array $priceResponse, $customerId);
}
