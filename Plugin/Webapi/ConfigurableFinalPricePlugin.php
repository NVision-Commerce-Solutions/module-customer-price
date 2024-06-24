<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\Configurable\FinalPriceResolver;
use Magento\ConfigurableProduct\Pricing\Price\FinalPrice;

class ConfigurableFinalPricePlugin
{
    public function __construct(private readonly FinalPriceResolver $finalPriceResolver) {}

    /**
     * @param FinalPrice $subject
     * @param $result
     */
    public function afterGetValue(FinalPrice $subject, $result)
    {
        return $this->finalPriceResolver->execute($subject->getProduct());
    }
}
