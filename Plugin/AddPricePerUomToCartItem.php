<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetPricePerUom;
use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetTierPricesPerUom;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;

class AddPricePerUomToCartItem
{
    public function __construct(
        private readonly GetPricePerUom $getPricePerUom,
        private readonly GetTierPricesPerUom $getTierPricesPerUom
    ) {}

    /**
     * @param AbstractItem $subject
     * @param $result
     * @param Item $item
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item)
    {
        $item = $this->resolveChildrenWithPricePerUom($item);

        $pricePerUom = $this->getPricePerUom->execute($item->getProductId());
        if (empty($pricePerUom)) {
            return $result;
        }

        $result['product_price_per_uom'] = $pricePerUom;
        $qty = $this->resolveQty($item);
        if ($qty === 1.0) {
            return $result;
        }

        $tierPrices = $this->getTierPricesPerUom->execute($item->getProductId());
        if (empty($tierPrices)) {
            return $result;
        }

        foreach ($tierPrices as $tierPrice) {
            if ($qty > $tierPrice['qty']) {
                $result['product_price_per_uom'] = $tierPrice['additional'];
            }
        }

        return $result;
    }

    private function resolveChildrenWithPricePerUom(Item $item): Item
    {
        if ($item->getHasChildren()) {
            foreach ($item->getChildren() as $child) {
                $pricePerUom = $this->getPricePerUom->execute($child->getProductId());
                if (!empty($pricePerUom)) {
                    return $child;
                }
            }
        }

        return $item;
    }

    private function resolveQty(Item $item)
    {
        return $item->getParentItem() ? $item->getParentItem()->getQty() : $item->getQty();
    }
}
