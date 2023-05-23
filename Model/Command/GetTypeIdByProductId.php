<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Magento\Framework\App\ResourceConnection;

class GetTypeIdByProductId
{
    private const TABLE_NAME = 'catalog_product_entity';
    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute($productId)
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, ['type_id'])
            ->where('entity_id = ?', $productId);

        $customerId = $connection->fetchOne($select);
        if (!$customerId) {
            return null;
        }

        return $customerId;
    }
}
