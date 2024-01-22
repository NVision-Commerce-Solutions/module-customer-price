<?php

namespace Commerce365\CustomerPrice\Api;

interface CustomerPriceInterface
{

    /**
     * @param mixed $productInfo
     * @param mixed $storeId
     * @param mixed $customerId
     * @param mixed $productId
     * @return mixed
     */
    public function getCustomerPrice($productInfo, $storeId, $customerId, $productId);
}
