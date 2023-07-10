<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Model\Command\GetTokenByCustomerId;
use Commerce365\CustomerPrice\Service\Customer\GenerateToken;
use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Helper\Session\CurrentCustomer;

class AddCustomerToken
{
    private CurrentCustomer $currentCustomer;
    private GenerateToken $generateToken;
    private GetTokenByCustomerId $getTokenByCustomerId;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param GenerateToken $generateToken
     * @param GetTokenByCustomerId $getTokenByCustomerId
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        GenerateToken $generateToken,
        GetTokenByCustomerId $getTokenByCustomerId
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->generateToken = $generateToken;
        $this->getTokenByCustomerId = $getTokenByCustomerId;
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
        $token = $this->getTokenByCustomerId->execute($customer->getId());
        if ($token) {
            $result['price_token'] = $token;

            return $result;
        }

        try {
            $token = $this->generateToken->execute($customer->getId());
        } catch (\Exception $e) {
            return $result;
        }

        $result['price_token'] = $token;

        return $result;
    }
}
