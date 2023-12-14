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
use Magento\Customer\Model\Session as CustomerSession;

class ProductPrice
{
    private GetProductPriceData $getProductPriceData;
    private Config $config;
    private GetMinimalSalableQty $getMinimalSalableQty;
    private GetPriceForQuantity $getPriceForQuantity;
    private IsPriceCallAvailable $isPriceCallAvailable;
    private CustomerSession $customerSession;


    /**
     * @param GetProductPriceData $getProductPriceData
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        GetProductPriceData $getProductPriceData,
        GetMinimalSalableQty $getMinimalSalableQty,
        GetPriceForQuantity $getPriceForQuantity,
        Config $config,
        IsPriceCallAvailable $isPriceCallAvailable,
        CustomerSession $customerSession
    ) {
        $this->getProductPriceData = $getProductPriceData;
        $this->config = $config;
        $this->getMinimalSalableQty = $getMinimalSalableQty;
        $this->getPriceForQuantity = $getPriceForQuantity;
        $this->isPriceCallAvailable = $isPriceCallAvailable;
        $this->customerSession = $customerSession;
    }

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
