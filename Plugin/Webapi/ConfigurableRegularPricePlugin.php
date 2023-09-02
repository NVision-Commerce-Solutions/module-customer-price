<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\Configurable\FinalPriceResolver;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableRegularPrice;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Amount\Base;

class ConfigurableRegularPricePlugin
{
    private FinalPriceResolver $finalPriceResolver;

    public function __construct(FinalPriceResolver $finalPriceResolver)
    {
        $this->finalPriceResolver = $finalPriceResolver;
    }

    /**
     * @param ConfigurableRegularPrice $subject
     * @param AmountInterface $result
     * @return AmountInterface
     */
    public function afterGetMinRegularAmount(
        ConfigurableRegularPrice $subject,
        AmountInterface $result
    ): AmountInterface {
        $regularPrice = $this->finalPriceResolver->execute($subject->getProduct());
        return new Base($regularPrice);
    }
}
