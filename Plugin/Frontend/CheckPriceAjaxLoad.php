<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Pricing\Render\FinalPriceBox;

class CheckPriceAjaxLoad
{
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param FinalPriceBox $subject
     * @param bool $result
     * @return bool
     */
    public function afterHasSpecialPrice(FinalPriceBox $subject, bool $result): bool
    {
        return $result && !$this->config->isAjaxEnabled();
    }

    /**
     * @param FinalPriceBox $subject
     * @param bool $result
     * @return bool
     */
    public function afterShowMinimalPrice(FinalPriceBox $subject, bool $result): bool
    {
        return $result && !$this->config->isAjaxEnabled();
    }
}
