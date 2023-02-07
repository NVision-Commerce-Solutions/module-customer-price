<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Framework\Pricing\Render\Amount;

class ListCheckAjaxPriceCall
{
    private GetPrice $getPrice;

    /**
     * @param GetPrice $getPrice
     */
    public function __construct(
        GetPrice $getPrice
    ) {
        $this->getPrice = $getPrice;
    }

    /**
     * @param Amount $subject
     * @param float|string $result
     * @return mixed|string
     */
    public function afterFormatCurrency(Amount $subject, $result)
    {
        return $this->getPrice->execute($result);
    }
}
