<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Model\Config;
use Commerce365\Core\Service\CurrentStore;
use Magento\Framework\App\ResourceConnection;

class GetCachedPriceData
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Config $config,
        private readonly CurrentStore $currentStore
    ) {}

    public function execute($productIds, $customerId): array
    {
        if (!$this->config->isCachingEnabled()) {
            return [];
        }

        $cacheHours = $this->config->getCacheHours();
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(CachedPrice::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, ['price_data', 'product_id'])
            ->where('product_id  IN(?)', $productIds)
            ->where('customer_id = ?', $customerId)
            ->where('store_id = ?', $this->currentStore->getId())
            ->where(sprintf('last_updated >= NOW() - INTERVAL %s HOUR', $cacheHours), '');

        return $connection->fetchAll($select);
    }
}
