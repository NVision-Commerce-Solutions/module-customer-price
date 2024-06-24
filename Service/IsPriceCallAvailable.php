<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Customer\Model\SessionFactory;

class IsPriceCallAvailable
{
    public function __construct(
        private readonly SessionFactory $customerSessionFactory,
        private readonly Config $config
    ) {}

    public function execute(): bool
    {
        return $this->customerSessionFactory->create()->isLoggedIn()
            && $this->config->isAjaxEnabled();
    }
}
