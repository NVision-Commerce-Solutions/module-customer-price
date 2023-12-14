<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session as CustomerSession;

class IsPriceCallAvailable
{
    private CustomerSession $customerSession;
    private RequestInterface $request;
    private Config $config;

    public function __construct(CustomerSession $customerSession, RequestInterface $request, Config $config)
    {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->config = $config;
    }

    public function execute(): bool
    {
        if ((false === $this->request->isXmlHttpRequest() && false === $this->request->isPost())
            && $this->request->getModuleName() !== 'loginascustomer') {
            return false;
       }

       return $this->config->isAjaxEnabled() && $this->customerSession->isLoggedIn();
    }
}
