<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context as HttpContext;

class ProductPlugin
{
    private Config $config;
    private HttpContext $httpContext;

    /**
     * @param Config $config
     * @param HttpContext $context
     */
    public function __construct(Config $config, HttpContext $context)
    {
        $this->config = $config;
        $this->httpContext = $context;
    }

    public function afterIsSalable(Product $subject, $result)
    {
        if ($this->config->isAjaxEnabled()
            && $this->config->isHidePricesGuest()
            && false === (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return false;
        }

        return $result;
    }

    public function afterGetData(Product $subject, $result, $key = '', $index = null)
    {
        if ($key === 'can_show_price'
            && $this->config->isHidePricesGuest()
            && $this->config->isAjaxEnabled()
            && false === (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return false;
        }

        return $result;
    }
}
