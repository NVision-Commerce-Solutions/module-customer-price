<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\View\GetConfigurableConfig;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class ConfigurableConfigProvider implements PriceInfoProviderInterface
{
    private GetConfigurableConfig $getConfigurableConfig;

    public function __construct(GetConfigurableConfig $getConfigurableConfig)
    {
        $this->getConfigurableConfig = $getConfigurableConfig;
    }

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            return $this->getConfigurableConfig->execute($product);
        }

        return '';
    }
}
