<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Framework\Pricing\Render\Amount;

class HidePriceAmount
{
    public function __construct(private readonly GetPrice $getPrice) {}

    /**
     * @param Amount $subject
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml(Amount $subject, $result): string
    {
        return $this->getPrice->execute($result);
    }
}
