<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\PriceInfoProvider;

use Commerce365\CustomerPrice\Service\View\GetPriceConfig;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class PriceConfigProvider implements PriceInfoProviderInterface
{
    private GetPriceConfig $getPriceConfig;

    public function __construct(GetPriceConfig $getPriceConfig)
    {
        $this->getPriceConfig = $getPriceConfig;
    }

    public function get(ProductInterface $product, $mainProductId = null, $mainProductType = ''): string
    {
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            return $this->getPriceConfig->execute($product);
        }

        if ((int) $product->getId() === (int) $mainProductId) {
            return $this->getPriceConfig->execute($product);
        }

        return '';
    }
}
