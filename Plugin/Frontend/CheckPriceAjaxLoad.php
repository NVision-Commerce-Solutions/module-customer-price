<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Catalog\Pricing\Render\FinalPriceBox;

class CheckPriceAjaxLoad
{
    public function __construct(private readonly Config $config) {}

    /**
     * @param FinalPriceBox $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterHasSpecialPrice(FinalPriceBox $subject, bool $result): bool
    {
        return $result && !$this->config->isAjaxEnabled();
    }

    /**
     * @param FinalPriceBox $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterShowMinimalPrice(FinalPriceBox $subject, bool $result): bool
    {
        return $result && !$this->config->isAjaxEnabled();
    }
}
