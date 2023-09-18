<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\InventoryConfiguration\Model\GetLegacyStockItem;

class GetMinimalSalableQty
{
    private GetLegacyStockItem $getLegacyStockItem;

    public function __construct(GetLegacyStockItem $getLegacyStockItem)
    {
        $this->getLegacyStockItem = $getLegacyStockItem;
    }

    /**
     * @param ProductInterface $product
     * @return float|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(ProductInterface $product): ?float
    {
        $stockItem = $this->getLegacyStockItem->execute($product->getSku());
        $minSaleQty = $stockItem->getMinSaleQty();
        return $minSaleQty > 0 ? $minSaleQty : null;
    }
}
