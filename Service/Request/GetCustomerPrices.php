<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request;

use Commerce365\Core\Model\AdvancedConfig;
use Commerce365\Core\Service\Request\PostFactory;
use Commerce365\Core\Service\CurrentStore;
use Commerce365\CustomerPrice\Service\Customer\CurrencyResolver;

class GetCustomerPrices implements GetCustomerPricesInterface
{
    public function __construct(
        private readonly CurrencyResolver $currencyResolver,
        private readonly AdvancedConfig $advancedConfig,
        private readonly CurrentStore $currentStore,
        private readonly PostFactory $postFactory
    ) {}

    public function execute($productIds, $customerId)
    {
        $storeId = $this->currentStore->getId();
        $post = $this->postFactory->create($storeId);

        $priceData = $post->execute($this->getMethod(), [
            'json' => $this->getJson($customerId, $productIds),
            'allow_redirects'=> ['strict' => true]
        ], $storeId);

        return !empty($priceData['priceLists']) ? $priceData['priceLists'] : [];
    }

    private function getMethod(): string
    {
        $currentStoreId = $this->currentStore->getId();
        if ($this->advancedConfig->isBCOAuth($currentStoreId) || $this->advancedConfig->isBCBasic($currentStoreId)) {
            return 'GetPrices_GetPricesById';
        }

        return 'price';
    }

    private function getJson($customerId, $productIds): array
    {
        $currentStoreId = $this->currentStore->getId();
        if ($this->advancedConfig->isBCOAuth($currentStoreId) || $this->advancedConfig->isBCBasic($currentStoreId)) {
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
