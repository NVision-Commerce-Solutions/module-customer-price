<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Service\Additional\AdditionalDataProviderInterface;

class UomDataProvider implements AdditionalDataProviderInterface
{
    public function get(array $priceInfo, $productId): string
    {
        return $priceInfo['uoM'] ?? '';
    }
}
