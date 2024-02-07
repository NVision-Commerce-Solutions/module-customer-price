<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\CustomerPrice\Service\Request\BusinessCentral\OAuthGetCustomerPrices;
use Magento\Framework\ObjectManagerInterface;

class GetCustomerPricesFactory
{
    private ObjectManagerInterface $objectManager;
    private AdvancedConfig $advancedConfig;

    public function __construct(ObjectManagerInterface $objectManager, AdvancedConfig $advancedConfig)
    {
        $this->objectManager = $objectManager;
        $this->advancedConfig = $advancedConfig;
    }

    public function create()
    {
        if ($this->advancedConfig->isBCOAuth()) {
            return $this->objectManager->create(OAuthGetCustomerPrices::class);
        }

        return $this->objectManager->create(GetCustomerPrices::class);
    }
}
