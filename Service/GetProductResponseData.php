<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Service\PriceInfoProvider\PriceInfoProviderInterface;
use Magento\Catalog\Model\ResourceModel\GetProductTypeById;
use RuntimeException;

class GetProductResponseData
{
    private array $priceInfoProviders;
    private GetProductTypeById $getProductTypeById;

    /**
     * @param GetProductTypeById $getProductTypeById
     * @param array $priceInfoProviders
     */
    public function __construct(
        GetProductTypeById $getProductTypeById,
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
