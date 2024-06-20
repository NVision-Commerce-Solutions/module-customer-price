<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Command\GetTypeIdByProductId;
use Commerce365\CustomerPrice\Service\PriceInfoProvider\PriceInfoProviderInterface;
use Magento\Framework\Exception\LocalizedException;

class GetProductResponseData
{
    public function __construct(
        private readonly GetTypeIdByProductId $getProductTypeById,
        private readonly array $priceInfoProviders
    ) {
    }

    public function execute($product, $productId): array
    {
        $productType = $productId ? $this->getProductTypeById->execute((int) $productId) : '';

        foreach($this->priceInfoProviders as $name => $priceInfoProvider) {
            if (!$priceInfoProvider instanceof  PriceInfoProviderInterface) {
                throw new LocalizedException(
                    __("Provider %1 should implements PriceInfoProviderInterface", get_class($priceInfoProvider))
                );
            }

            $result[$name] = $priceInfoProvider->get($product, $productId, $productType);
        }

        $result['productId'] = $product->getId();

        return $result;
    }


}
