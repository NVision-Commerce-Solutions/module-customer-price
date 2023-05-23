<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\ResourceConnection;

class GetCachedPriceData
{
    private ResourceConnection $resourceConnection;
    private Config $config;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Config $config
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $config
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->config = $config;
    }

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
            ->where(sprintf('last_updated >= NOW() - INTERVAL %s HOUR', $cacheHours), '');

        return $connection->fetchAll($select);
    }
}
