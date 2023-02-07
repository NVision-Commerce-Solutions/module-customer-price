<?php

namespace Commerce365\CustomerPrice\Api;

interface CustomerPriceInterface
{

    /**
     * @param mixed $productInfo
     * @param mixed $storeId
     * @param mixed $customerToken
     * @param mixed $productId
     * @return mixed
     */
    public function getCustomerPrice($productInfo, $storeId, $customerToken, $productId);
}
