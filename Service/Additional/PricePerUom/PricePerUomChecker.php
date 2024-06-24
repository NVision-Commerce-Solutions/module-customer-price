<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Model\Config;

class PricePerUomChecker
{
    public function __construct(private readonly Config $config) {}

    public function canShow(): bool
    {
        return $this->config->showPricePerUom();
    }
}
