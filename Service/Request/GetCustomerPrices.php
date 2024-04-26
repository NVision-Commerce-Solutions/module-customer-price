<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Service\Request\PostFactory;
use Commerce365\CustomerPrice\Service\Customer\CurrencyResolver;

class GetCustomerPrices implements GetCustomerPricesInterface
{
    private CurrencyResolver $currencyResolver;
    private AdvancedConfig $advancedConfig;
    private PostFactory $postFactory;

    /**
     * @param CurrencyResolver $currencyResolver
     * @param AdvancedConfig $advancedConfig
     * @param PostFactory $postFactory
     */
    public function __construct(
        CurrencyResolver $currencyResolver,
        AdvancedConfig $advancedConfig,
        PostFactory $postFactory
    ) {
        $this->currencyResolver = $currencyResolver;
        $this->advancedConfig = $advancedConfig;
        $this->postFactory = $postFactory;
    }

    public function execute($productIds, $customerId)
    {
        $post = $this->postFactory->create();

        $priceData = $post->execute($this->getMethod(), [
            'json' => $this->getJson($customerId, $productIds),
            'allow_redirects'=> ['strict' => true]
        ]);

        return !empty($priceData['priceLists']) ? $priceData['priceLists'] : [];
    }

    private function getMethod(): string
    {
        if ($this->advancedConfig->isBCOAuth() || $this->advancedConfig->isBCBasic()) {
            return 'GetPrices_GetPricesById';
        }

        return 'price';
    }

    private function getJson($customerId, $productIds): array
    {
        if ($this->advancedConfig->isBCOAuth() || $this->advancedConfig->isBCBasic()) {
            return [
                'customerId' => (int) $customerId,
                'productIds' => array_values(array_map('intval', $productIds)),
                'currencyCode' => $this->currencyResolver->resolve($customerId),
            ];
        }

        return [
            'CustomerId' => (int) $customerId,
            'ProductIds' => array_values(array_map('intval', $productIds)),
            'CurrencyCode' => $this->currencyResolver->resolve($customerId),
        ];
    }
}
