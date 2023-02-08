<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Magento\Framework\App\ResourceConnection;

class CleanCacheCustomer
{
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute($customerId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(CachedPrice::TABLE_NAME);
        $connection->delete($tableName, ['customer_id = ?' => $customerId]);
    }
}
