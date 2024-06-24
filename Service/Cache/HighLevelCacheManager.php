<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\Core\Service\Customer\GetParentCustomerId;
use Commerce365\CustomerPrice\Model\Command\GetPreparedPriceData;
use Commerce365\CustomerPrice\Model\Command\SetPreparedPriceData;
use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\CurrentCustomer;
use Commerce365\CustomerPrice\Service\CurrentStore;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\SerializerInterface;

class HighLevelCacheManager
{
    public const TABLE_NAME = 'commerce365_prepared_price';

    public function __construct(
        private readonly GetPreparedPriceData $getPreparedPriceData,
        private readonly GetParentCustomerId $getParentCustomerId,
        private readonly SetPreparedPriceData $setPreparedPriceData,
        private readonly SerializerInterface $serializer,
        private readonly CurrentStore $currentStore,
        private readonly Config $config,
        private readonly CurrentCustomer $currentCustomer
    ) {}

    public function get(Product $product, int $productId): array
    {
        if (!$this->isEnabled($productId)) {
            return [];
        }

        $value = $this->getPreparedPriceData->execute(
            $product->getId(),
            $this->getCustomerId(),
            $this->currentStore->getId()
        );
        if (!$value) {
            return [];
        }

        return $this->serializer->unserialize($value);
    }

    public function set(Product $product, int $productId, array $data): void
    {
        if (empty($data) || !$this->isEnabled($productId)) {
            return;
        }

        $dataToInsert = [
            'price_data' => $this->serializer->serialize($data),
            'product_id' => $product->getId(),
            'customer_id' => $this->getCustomerId(),
            'store_id' => $this->currentStore->getId()
        ];

        $this->setPreparedPriceData->execute($dataToInsert);
    }

    private function isEnabled($productId): bool
    {
        return $productId === 0 && $this->config->isHighLevelCachingEnabled();
    }

    private function getCustomerId(): int
    {
        $customerId = $this->currentCustomer->getId();

        return (int) $this->getParentCustomerId->execute($customerId);
    }
}
