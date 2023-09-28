<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Customer\Model\SessionFactory;

class IsPriceCallAvailable
{
    private SessionFactory $customerSessionFactory;
    private Config $config;

    public function __construct(SessionFactory $customerSessionFactory, Config $config)
    {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->config = $config;
    }

    public function execute(): bool
    {
        return $this->customerSessionFactory->create()->isLoggedIn()
            && $this->config->isAjaxEnabled();
    }
}
