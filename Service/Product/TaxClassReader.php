<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Product;

use Magento\Catalog\Model\ResourceModel\Product\Action as ProductAction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class TaxClassReader
{
    public function __construct(
        private readonly ProductAction $productAction,
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * Get a product’s tax class ID without loading the full product model.
     *
     * @param int $productId
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function getTaxClassId(int $productId): ?int
    {
        $storeId = (int) $this->storeManager->getStore()->getId();

        $value = $this->productAction->getAttributeRawValue($productId, 'tax_class_id', $storeId);

        return $value !== false ? (int) $value : null;
    }
}
