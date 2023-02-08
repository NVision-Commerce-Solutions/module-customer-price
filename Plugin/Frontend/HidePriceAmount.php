<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Framework\Pricing\Render\Amount;

class HidePriceAmount
{
    private GetPrice $getPrice;

    public function __construct(GetPrice $getPrice)
    {
        $this->getPrice = $getPrice;
    }

    /**
     * @param Amount $subject
     * @return array
     */
    public function afterToHtml(Amount $subject, $result)
    {
        return $this->getPrice->execute($result);
    }
}
