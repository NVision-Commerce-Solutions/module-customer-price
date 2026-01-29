<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\GetMinimalSalableQty;
use Commerce365\CustomerPrice\Service\GetPriceForQuantity;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Commerce365\CustomerPrice\Service\IsPriceCallAvailable;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Customer\Model\Session;

class ProductPrice
{
    public function __construct(
        private readonly Session $customerSession,
        private readonly GetProductPriceData $getProductPriceData,
        private readonly GetMinimalSalableQty $getMinimalSalableQty,
        private readonly GetPriceForQuantity $getPriceForQuantity,
        private readonly Config $config,
        private readonly IsPriceCallAvailable $isPriceCallAvailable
    ) {}

    /**
     * @param Product $subject
     * @param $result
     * @return mixed
     */
    public function afterGetPrice(Product $subject, $result)
    {
        if (!$this->isPriceCallAvailable->execute() || $subject->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        try {
            $customerId = $this->customerSession->getCustomerId();

            $priceData = $this->getProductPriceData->execute($subject->getId(), $customerId);
            if ($this->config->useMinSalableQty()) {
                $minSalableQtyPrice = $this->getMinSalableQtyPrice($subject, $priceData);
                if ($minSalableQtyPrice > 0) {
                    return $minSalableQtyPrice;
                }
            }

            if ($priceData->getPrice() <= 0) {
                return $result;
            }

            return $priceData->getPrice();
        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * @param Product $product
     * @param CachedPrice $priceData
     * @return float|int|mixed
     */
    private function getMinSalableQtyPrice(Product $product, CachedPrice $priceData)
    {
        $minSalableQty = $this->getMinimalSalableQty->execute($product);
        if ($minSalableQty > 1) {
            return $this->getPriceForQuantity->getPriceByQtyAndPriceData($priceData, $minSalableQty);
        }

        return 0;
    }

    /**
     * @param Product $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSpecialPrice(Product $subject, $result)
    {
        if (!$this->isPriceCallAvailable->execute() || $subject->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        try {
            $priceData = $this->getProductPriceData->execute(
                $subject->getId(),
                $this->customerSession->getCustomerId()
            );

            if (!$priceData->getSpecialPrice()) {
                return $priceData->getPrice() > 0 ? null : $result;
            }

            return $priceData->getSpecialPrice();
        } catch (\Exception $e) {
            return $result;
        }
    }
}
