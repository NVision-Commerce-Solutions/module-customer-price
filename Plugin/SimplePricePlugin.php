<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Plugin\Frontend\Product;
use Commerce365\CustomerPrice\Service\GetPriceForQuantity;
use Magento\Catalog\Model\Product\Type\Price;
use Magento\Customer\Model\SessionFactory;

class SimplePricePlugin
{
    private SessionFactory $customerSessionFactory;
    private GetPriceForQuantity $getPriceForQuantity;
    private Config $config;

    /**
     * @param SessionFactory $customerSessionFactory
     * @param GetPriceForQuantity $getPriceForQuantity
     * @param Config $config
     */
    public function __construct(
        SessionFactory $customerSessionFactory,
        GetPriceForQuantity $getPriceForQuantity,
        Config $config
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->getPriceForQuantity = $getPriceForQuantity;
        $this->config = $config;
    }

    /**
     * @param Price $subject
     * @param mixed $result
     * @param float $qty
     * @param Product $product
     * @return array
     */
    public function afterGetTierPrice(Price $subject, $result, $qty, $product)
    {
        if (!$this->config->isAjaxEnabled()) {
            return $result;
        }

        $customerId = $this->customerSessionFactory->create()->getCustomerId();
        if (!$customerId) {
            return $result;
        }

        return $this->getPriceForQuantity->execute($product, $customerId, $qty);
    }
}
