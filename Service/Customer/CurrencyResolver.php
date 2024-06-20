<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Customer;

use Magento\Store\Model\StoreManagerInterface;

class CurrencyResolver
{
    public function __construct(private readonly StoreManagerInterface $storeManager) {}

    public function resolve($customerId)
    {
        return $this->getSystemCurrency();
    }

    private function getSystemCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
