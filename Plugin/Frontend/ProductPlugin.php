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
            && !$this->customerSessionFactory->create()->isLoggedIn()
            && $this->config->isHidePricesGuest()) {
            return false;
        }

        return $result;
    }
}
