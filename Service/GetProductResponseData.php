<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\GetTypeIdByProductId;
use Commerce365\CustomerPrice\Service\PriceInfoProvider\PriceInfoProviderInterface;
use RuntimeException;

class GetProductResponseData
{
    private array $priceInfoProviders;
    private GetTypeIdByProductId $getProductTypeById;

    /**
     * @param GetTypeIdByProductId $getProductTypeById
     * @param array $priceInfoProviders
     */
    public function __construct(
        GetTypeIdByProductId $getProductTypeById,
        array $priceInfoProviders
    ) {
        $this->priceInfoProviders = $priceInfoProviders;
        $this->getProductTypeById = $getProductTypeById;
    }

    public function execute($product, $productId): array
    {
        $result = [];
        $productType = $productId ? $this->getProductTypeById->execute((int) $productId) : '';

        foreach($this->priceInfoProviders as $name => $priceInfoProvider) {
            if (!$priceInfoProvider instanceof  PriceInfoProviderInterface) {
                throw new RuntimeException(
                    __("Provider %1 should implements PriceInfoProviderInterface", get_class($priceInfoProvider))
                );
            }

            $result[$name] = $priceInfoProvider->get($product, $productId, $productType);
        }

        $result['productId'] = $product->getId();

        return $result;
    }
}
