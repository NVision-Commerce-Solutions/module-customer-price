<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Plugin\Frontend;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\PriceBox\GetPrice;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\GroupedProduct\Pricing\Price\FinalPrice;

class GroupedProductPlugin
{
    private Config $config;
    private SessionFactory $customerSessionFactory;
    private RequestInterface $request;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config,
        SessionFactory $customerSessionFactory,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->request = $request;
    }

    /**
     * @param FinalPrice $subject
     * @param Product $result
     * @return Product
     */
    public function afterGetMinProduct(FinalPrice $subject, Product $result)
    {
        if (!$this->config->isAjaxEnabled() || $this->request->isXmlHttpRequest()) {
            return $result;
        }

        if (!$this->config->isHidePricesGuest() && !$this->customerSessionFactory->create()->isLoggedIn()) {
            return $result;
        }

        return null;
    }
}
