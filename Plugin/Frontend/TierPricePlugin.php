<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Customer\Model\Session;

class TierPricePlugin
{
    public function __construct(
        private readonly Session $customerSession,
        private readonly Config $config
    ) {}

    public function afterGetTierPriceList(TierPrice $subject, $result)
    {
        if ($this->config->isAjaxEnabled()) {
            return [];
        }

        if (!$this->customerSession->isLoggedIn() && $this->config->isHidePricesGuest()) {
            return [];
        }

        return $result;
    }
}
