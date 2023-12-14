<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceBox;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\RequestInterface;

class GetPrice
{
    private Config $config;
    private RequestInterface $request;
    private HttpContext $httpContext;

    /**
     * @param Config $config
     * @param RequestInterface $request
     */
    public function __construct(
        Config $config,
        RequestInterface $request,
        HttpContext $context
    ) {
        $this->config = $config;
        $this->request = $request;
        $this->httpContext = $context;
    }

    public function execute($default)
    {
        if (!$this->config->isAjaxEnabled() || $this->request->isXmlHttpRequest()) {
            return $default;
        }

        if (!$this->config->isHidePricesGuest() &&
            false === (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
        ) {
            return $default;
        }

        if ($this->config->isHidePricesGuest() &&
            false === (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
        ) {
            return '';
        }

        return '<div class="commerce365-price-placeholder"></div>';
    }
}
