<?php

namespace Commerce365\CustomerPrice\ViewModel\Cache;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Permissions implements ArgumentInterface
{
    public function __construct(private readonly AuthorizationInterface $authorization) {}

    public function hasAccessToFlushPriceCache(): bool
    {
        return $this->authorization->isAllowed('Commerce365_CustomerPrice::cache_flush');
    }
}
