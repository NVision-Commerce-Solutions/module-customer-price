<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetPricePerUom;
use Commerce365\CustomerPrice\Service\Additional\PricePerUom\GetTierPricesPerUom;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;

class AddPricePerUomToCartItem
{
    private GetPricePerUom $getPricePerUom;
    private GetTierPricesPerUom $getTierPricesPerUom;

    public function __construct(GetPricePerUom $getPricePerUom, GetTierPricesPerUom $getTierPricesPerUom)
    {
        $this->getPricePerUom = $getPricePerUom;
        $this->getTierPricesPerUom = $getTierPricesPerUom;
    }

    /**
     * @param AbstractItem $subject
     * @param $result
     * @param Item $item
     */
    public function afterGetItemData(AbstractItem $subject, $result, Item $item)
    {
        $pricePerUom = $this->getPricePerUom->execute($item->getProductId());
        if (empty($pricePerUom)) {
            return $result;
        }

        $result['product_price_per_uom'] = $pricePerUom;
        if ($item->getQty() === 1.0) {
            return $result;
        }

        $tierPrices = $this->getTierPricesPerUom->execute($item->getProductId());
        if (empty($tierPrices)) {
            return $result;
        }

        foreach ($tierPrices as $tierPrice) {
            if ($item->getQty() > $tierPrice['qty']) {
                $result['product_price_per_uom'] = $tierPrice['additional'];
            }
        }

        return $result;
    }
}
