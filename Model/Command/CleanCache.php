<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Magento\Framework\App\ResourceConnection;

class CleanCache
{
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(CachedPrice::TABLE_NAME);
        $connection->truncateTable($tableName);
    }
}
