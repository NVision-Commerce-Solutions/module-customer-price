<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheManager;
use Magento\Framework\App\ResourceConnection;

class CleanCache
{
    public function __construct(private readonly ResourceConnection $resourceConnection) {}

    public function execute()
    {
        $tableName = $this->resourceConnection->getTableName(CachedPrice::TABLE_NAME);
        $this->resourceConnection->getConnection()->truncateTable($tableName);

        $tableName = $this->resourceConnection->getTableName(HighLevelCacheManager::TABLE_NAME);
        $this->resourceConnection->getConnection()->truncateTable($tableName);
    }
}
