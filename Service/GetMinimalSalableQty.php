<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\LocalizedException;

class GetMinimalSalableQty
{
    private StockRegistryInterface $stockRegistry;

    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param ProductInterface $product
     * @return float|null
     * @throws LocalizedException
     */
    public function execute(ProductInterface $product): ?float
    {
        $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
        $minSaleQty = $stockItem->getMinSaleQty();
        return $minSaleQty > 0 ? $minSaleQty : null;
    }
}
