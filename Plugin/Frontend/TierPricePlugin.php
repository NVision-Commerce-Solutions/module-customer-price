<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\Framework\App\Http\Context as HttpContext;

class TierPricePlugin
{
    private Config $config;
    private HttpContext $httpContext;

    public function __construct(
        Config $config,
        HttpContext $context
    ) {
        $this->config = $config;
        $this->httpContext = $context;
    }

    public function afterGetTierPriceList(TierPrice $subject, $result)
    {
        if ($this->config->isAjaxEnabled()) {
            return [];
        }

        if ($this->config->isHidePricesGuest() &&
            false === (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)
        ) {
            return [];
        }

        return $result;
    }
}
