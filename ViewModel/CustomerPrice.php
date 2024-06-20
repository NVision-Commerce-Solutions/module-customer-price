<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\ViewModel;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomerPrice implements ArgumentInterface
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly Context $httpContext,
        private readonly Registry $registry,
        private readonly Config $config,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getConfig(): string
    {
        $currentProduct = $this->registry->registry('product');
        $config = [
            'storeId' => $this->storeManager->getStore()->getId(),
            'productId' => $currentProduct ? $currentProduct->getId() : ''
        ];

        return $this->serializer->serialize($config);
    }

    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return bool
     */
    public function isAjaxEnabled(): bool
    {
        return $this->config->isAjaxEnabled();
    }

    public function preselectConfigurable(): bool
    {
        return $this->config->preselectConfigurable();
    }
}
