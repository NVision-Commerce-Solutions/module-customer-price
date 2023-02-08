<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceBox;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;

class GetPrice
{
    private Config $config;
    private SessionFactory $customerSessionFactory;
    private RequestInterface $request;

    /**
     * @param Config $config
     * @param SessionFactory $customerSessionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        SessionFactory $customerSessionFactory,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->request = $request;
    }

    public function execute($default)
    {
        if (!$this->config->isAjaxEnabled() || $this->request->isXmlHttpRequest()) {
            return $default;
        }

        if (!$this->config->isHidePricesGuest() && !$this->customerSessionFactory->create()->isLoggedIn()) {
            return $default;
        }

        if ($this->config->isHidePricesGuest() && !$this->customerSessionFactory->create()->isLoggedIn()) {
            return '';
        }

        return '<div class="commerce365-price-placeholder"></div>';
    }
}
