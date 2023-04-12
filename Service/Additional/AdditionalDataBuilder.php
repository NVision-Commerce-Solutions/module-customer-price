<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional;

use Commerce365\CustomerPrice\Model\Command\GetTypeIdByProductId;
use Magento\Catalog\Model\Product\Type;

class AdditionalDataBuilder
{
    private GetTypeIdByProductId $getTypeIdByProductId;

    public function __construct(GetTypeIdByProductId $getTypeIdByProductId)
    {
        $this->getTypeIdByProductId = $getTypeIdByProductId;
    }

    public function build(array $priceInfo, $productId): array
    {
        return [
            'pricePerUOM' => $this->getPricePerUom($priceInfo, $productId),
            'UOM' => $priceInfo['uoM'] ?? ''
        ];
    }

    private function getPricePerUom(array $priceInfo, $productId)
    {
        $pricePerUom = '';
        if ($this->getTypeIdByProductId->execute($productId) === Type::DEFAULT_TYPE) {
            $pricePerUom = $priceInfo['pricePerUoM'] ?? '';
        }

        return $pricePerUom;
    }
}
