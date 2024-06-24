<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheManager;
use Magento\Framework\App\ResourceConnection;

class GetPreparedPriceData
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Config $config
    ) {}

    public function execute($productId, $customerId, $storeId): string|bool
    {
        if (!$this->config->isCachingEnabled()) {
            return '';
        }

        $cacheHours = $this->config->getCacheHours();
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(HighLevelCacheManager::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, ['price_data'])
            ->where('product_id = ?', $productId)
            ->where('customer_id = ?', $customerId)
            ->where('store_id = ?', $storeId)
            ->where(sprintf('last_updated >= NOW() - INTERVAL %s HOUR', $cacheHours), '');

        return  $connection->fetchOne($select);
    }
}
