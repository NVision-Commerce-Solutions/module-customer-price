<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceBox;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;

class GetPrice
{
    public function __construct(
        private readonly Config $config,
        private readonly Session $customerSession,
        private readonly RequestInterface $request
    ) {}

    public function execute($default)
    {
        if (!$this->config->isAjaxEnabled() || $this->request->isXmlHttpRequest()) {
            return $default;
        }

        if (!$this->config->isHidePricesGuest() && !$this->customerSession->isLoggedIn()) {
            return $default;
        }

        if ($this->config->isHidePricesGuest() && !$this->customerSession->isLoggedIn()) {
            return '';
        }

        return '<div class="commerce365-price-placeholder"></div>';
    }
}
