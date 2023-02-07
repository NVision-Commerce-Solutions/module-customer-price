<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Customer\Model\SessionFactory;

class TierPricePlugin
{
    private SessionFactory $customerSessionFactory;
    private Config $config;

    public function __construct(
        SessionFactory $customerSessionFactory,
        Config $config
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->config = $config;
    }

    public function afterGetTierPriceList(TierPrice $subject, $result)
    {
        if ($this->config->isAjaxEnabled()) {
            return [];
        }

        if (!$this->customerSessionFactory->create()->isLoggedIn() && $this->config->isHidePricesGuest()) {
            return [];
        }

        return $result;
    }
}
