<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional\PricePerUom;

use Commerce365\CustomerPrice\Model\Command\GetTypeIdByProductId;
use Commerce365\CustomerPrice\Service\Additional\AdditionalDataProviderInterface;
use Magento\Catalog\Model\Product\Type;

class DataProvider implements AdditionalDataProviderInterface
{
    private GetTypeIdByProductId $getTypeIdByProductId;

    public function __construct(GetTypeIdByProductId $getTypeIdByProductId)
    {
        $this->getTypeIdByProductId = $getTypeIdByProductId;
    }

    public function get(array $priceInfo, $productId): string
    {
        $pricePerUom = '';
        if ($this->getTypeIdByProductId->execute($productId) === Type::DEFAULT_TYPE) {
            $pricePerUom = $priceInfo['pricePerUoM'] ?? '';
        }

        return (string) $pricePerUom;
    }
}
