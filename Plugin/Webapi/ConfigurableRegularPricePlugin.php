<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\Configurable\FinalPriceResolver;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableRegularPrice;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Amount\Base;

class ConfigurableRegularPricePlugin
{
    public function __construct(private readonly FinalPriceResolver $finalPriceResolver) {}

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
