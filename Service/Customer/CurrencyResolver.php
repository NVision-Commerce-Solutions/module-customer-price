<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class CurrencyResolver
{
    private CustomerRepositoryInterface $customerRepository;
    private StoreManagerInterface $storeManager;

    public function __construct(CustomerRepositoryInterface $customerRepository, StoreManagerInterface $storeManager)
    {
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    public function resolve($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return $this->getSystemCurrency();
        }

        $customerCurrency = $customer->getCustomAttribute('bc_customer_currency');

        return $customerCurrency ? $customerCurrency->getValue() : $this->getSystemCurrency();
    }

    private function getSystemCurrency()
    {
        return $this->storeManager->getStore()->getCurrentCurrency()->getCode();
    }
}
