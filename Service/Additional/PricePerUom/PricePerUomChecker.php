<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Model\Config;

class PricePerUomChecker
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function canShow(): bool
    {
        if ($this->config->showPricePerUom()) {
            return true;
        }

        return false;
    }
}
