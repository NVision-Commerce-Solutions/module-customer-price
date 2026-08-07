<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;

class ProductPlugin
{
    public function __construct(
        private readonly Config $config,
        private readonly Session $customerSession
    ) {}

    public function afterIsSalable(Product $subject, $result)
    {
        if ($this->config->isAjaxEnabled()
            && $this->config->isHidePricesGuest()
            && !$this->customerSession->isLoggedIn()) {
            return false;
        }

        return $result;
    }

    public function afterGetData(Product $subject, $result, $key = '')
    {
        if ($key === 'can_show_price'
            && $this->config->isHidePricesGuest()
            && $this->config->isAjaxEnabled()
            && !$this->customerSession->isLoggedIn()) {
            return false;
        }

        return $result;
    }
}
