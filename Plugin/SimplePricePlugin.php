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
    public function __construct(
        private readonly SessionFactory $customerSessionFactory,
        private readonly GetPriceForQuantity $getPriceForQuantity,
        private readonly Config $config
    ) {}

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
