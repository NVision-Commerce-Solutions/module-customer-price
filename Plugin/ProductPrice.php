<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\GetProductPriceData;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Customer\Model\SessionFactory;

class ProductPrice
{
    private SessionFactory $customerSessionFactory;
    private GetProductPriceData $getProductPriceData;
    private Config $config;

    /**
     * @param SessionFactory $customerSessionFactory
     * @param GetProductPriceData $getProductPriceData
     * @param Config $config
     */
    public function __construct(
        SessionFactory $customerSessionFactory,
        GetProductPriceData $getProductPriceData,
        Config $config
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->getProductPriceData = $getProductPriceData;
        $this->config = $config;
    }

    /**
     * @param Product $subject
     * @param $result
     * @return mixed
     */
    public function afterGetPrice(Product $subject, $result)
    {
        if (!$this->isPriceCallAvailable() || $subject->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        try {
            $priceData = $this->getProductPriceData->execute(
                $subject->getId(),
                $this->customerSessionFactory->create()->getCustomerId()
            );

            if ($priceData->getPrice() <= 0) {
                return $result;
            }

            return $priceData->getPrice();
        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * @param Product $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSpecialPrice(Product $subject, $result)
    {
        if (!$this->isPriceCallAvailable() || $subject->getTypeId() !== Type::DEFAULT_TYPE) {
            return $result;
        }

        try {
            $priceData = $this->getProductPriceData->execute(
                $subject->getId(),
                $this->customerSessionFactory->create()->getCustomerId()
            );

            if (!$priceData->getSpecialPrice()) {
                return null;
            }

            return $priceData->getSpecialPrice();
        } catch (\Exception $e) {
            return $result;
        }
    }

    /**
     * @return bool
     */
    private function isPriceCallAvailable(): bool
    {
        return $this->customerSessionFactory->create()->isLoggedIn()
            && $this->config->isAjaxEnabled();
    }
}
