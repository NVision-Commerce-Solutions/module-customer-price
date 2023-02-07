<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\ViewModel;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomerPrice implements ArgumentInterface
{
    private StoreManagerInterface $storeManager;
    private Config $config;
    private Registry $registry;
    private Context $httpContext;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Context $httpContext
     * @param Registry $registry
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Context $httpContext,
        Registry $registry,
        Config $config
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->registry = $registry;
        $this->httpContext = $httpContext;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $currentProduct = $this->registry->registry('product');
        return [
            'storeId' => $this->storeManager->getStore()->getId(),
            'productId' => $currentProduct ? $currentProduct->getId() : ''
        ];
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
}
