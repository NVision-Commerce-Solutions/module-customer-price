<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheManager;
use Magento\Framework\App\ResourceConnection;

class CleanCache
{
    public function __construct(private readonly ResourceConnection $resourceConnection) {}

    public function execute(): void
    {
        if($this->resourceConnection->getConnection()->isTableExists(CachedPrice::TABLE_NAME)) {
            $tableName = $this->resourceConnection->getTableName(CachedPrice::TABLE_NAME);
            $this->resourceConnection->getConnection()->truncateTable($tableName);
        }

        if($this->resourceConnection->getConnection()->isTableExists(HighLevelCacheManager::TABLE_NAME)) {
            $tableName = $this->resourceConnection->getTableName(HighLevelCacheManager::TABLE_NAME);
            $this->resourceConnection->getConnection()->truncateTable($tableName);
        }
    }
}
