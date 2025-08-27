<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;

class GetPriceForQuantity
{
    public function __construct(
        private readonly GetProductPriceData $getProductPriceData,
        private readonly GroupManagementInterface $groupManagement
    ) {}

    public function execute(Product $product, $customerId, $qty = null)
    {
        $priceData = $this->getProductPriceData->execute($product->getId(), $customerId);
        if (empty($priceData->getTierPrices())) {
            if ($qty !== null) {
                return $priceData->getPrice() > 0 ? $priceData->getPrice() : $product->getPrice();
            }

            return [
                [
                    'price' => $priceData->getPrice(),
                    'website_price' => $priceData->getPrice(),
                    'price_qty' => 1,
                    'cust_group' => $this->getAllCustomerGroupsId(),
                ]
            ];
        }

        return $this->getPriceByQtyAndPriceData($priceData, $qty);
    }

    public function getPriceByQtyAndPriceData(CachedPrice $priceData, $qty)
    {
        $prevQty = 0;
        $prevPrice = $priceData->getPrice();
        foreach ($priceData->getTierPrices() as $tierPrice) {
            if ($qty < $tierPrice['qty']) {
                // tier is higher than product qty
                continue;
            }
            if ($tierPrice['qty'] < $prevQty) {
                // higher tier qty already found
                continue;
            }

            if ($tierPrice['price'] < $prevPrice) {
                $prevPrice = $tierPrice['price'];
                $prevQty = $tierPrice['qty'];
            }
        }

        return $prevPrice;
    }

    /**
     * Gets the CUST_GROUP_ALL id
     *
     * @return int
     */
    protected function getAllCustomerGroupsId()
    {
        // ex: 32000
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }
}
