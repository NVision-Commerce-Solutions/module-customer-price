<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\SessionFactory;

class ProductPlugin
{
    private Config $config;
    private SessionFactory $customerSessionFactory;

    /**
     * @param Config $config
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct(Config $config, SessionFactory $customerSessionFactory)
    {
        $this->config = $config;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    public function afterIsSalable(Product $subject, $result)
    {
        if ($this->config->isAjaxEnabled()
            && $this->config->isHidePricesGuest()
            && !$this->customerSessionFactory->create()->isLoggedIn()) {
            return false;
        }

        return $result;
    }

    public function afterGetData(Product $subject, $result, $key = '', $index = null)
    {
        if ($key === 'can_show_price'
            && $this->config->isHidePricesGuest()
            && $this->config->isAjaxEnabled()
            && !$this->customerSessionFactory->create()->isLoggedIn()) {
            return false;
        }

        return $result;
    }
}
