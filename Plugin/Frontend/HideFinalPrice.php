<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Framework\Pricing\Amount\Base;

class HideFinalPrice
{
    private GetPrice $getPrice;

    /**
     * @param GetPrice $getPrice
     */
    public function __construct(GetPrice $getPrice)
    {
        $this->getPrice = $getPrice;
    }

    /**
     * @param Base $subject
     * @param float $result
     * @return float
     */
    public function afterGetValue(Base $subject, $result)
    {
        return $this->getPrice->execute($result);
    }
}
