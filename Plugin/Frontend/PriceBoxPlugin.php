<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\Render\PriceBox;

class PriceBoxPlugin
{
    private GetPrice $getPrice;

    public function __construct(GetPrice $getPrice)
    {
        $this->getPrice = $getPrice;
    }

    /**
     * @param PriceBox $subject
     * @param $result
     * @param AmountInterface $amount
     * @param array $arguments
     */
    public function afterRenderAmount(PriceBox $subject, $result, AmountInterface $amount, array $arguments = [])
    {
        return $this->getPrice->execute($result);
    }
}
