<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Webapi;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetPricePerUom;
use Magento\Catalog\Model\Product;

class AdditionalDataPlugin
{
    private GetPricePerUom $getPricePerUom;

    public function __construct(GetPricePerUom $getPricePerUom)
    {
        $this->getPricePerUom = $getPricePerUom;
    }

    public function afterGetData(Product $subject, $result, $key = null)
    {
        if ($key !== 'additional_price_data') {
            return $result;
        }

        $additionalData = $this->getPricePerUom->execute($subject->getId());

        return !empty($additionalData) ? $additionalData : $result;
    }
}
