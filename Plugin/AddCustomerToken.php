<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Service\Customer\GenerateToken;
use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;

class AddCustomerToken
{
    private CurrentCustomer $currentCustomer;
    private GenerateToken $generateToken;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param GenerateToken $generateToken
     */
    public function __construct(CurrentCustomer $currentCustomer, GenerateToken $generateToken)
    {
        $this->currentCustomer = $currentCustomer;
        $this->generateToken = $generateToken;
    }

    /**
     * @param Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(Customer $subject, array $result): array
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return $result;
        }

        $customer = $this->currentCustomer->getCustomer();
        try {
            $token = $this->generateToken->execute($customer->getId());
        } catch (\Exception $e) {
            return $result;
        }

        $result['price_token'] = $token;

        return $result;
    }
}
