<?php

namespace Commerce365\CustomerPrice\Service\Request;

interface GetCustomerPricesInterface
{
    public function execute($productIds, $customerId);
}
