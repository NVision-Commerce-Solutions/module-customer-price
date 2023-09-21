<?php
declare(strict_types=1);
namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\ResourceModel\GetProductTypeById;
use Commerce365\CustomerPrice\Service\PriceInfoProvider\PriceInfoProviderInterface;
use Commerce365\CustomerPrice\Model\ResourceModel\GetProductTypeByIdFactory;
use RuntimeException;

class GetProductResponseData
{
    /**
     * @var array
     */
    private array $priceInfoProviders;

    /**
     * Type depends on factory result
     *
     * @var GetProductTypeById
     */
    private $getProductTypeById;

    /**
     * @param GetProductTypeByIdFactory $getProductTypeByIdFactory
     * @param array $priceInfoProviders
     */
    public function __construct(
        GetProductTypeByIdFactory $getProductTypeByIdFactory,
        array $priceInfoProviders
    ) {
        $this->priceInfoProviders = $priceInfoProviders;
        /** Added for Magento <2.4.5 compatibility */
        $this->getProductTypeById = $getProductTypeByIdFactory->create();
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
