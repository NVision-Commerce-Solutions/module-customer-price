<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;

class GetTypeIdByProductId
{
    private const TABLE_NAME = 'catalog_product_entity';

    public function __construct(private readonly ResourceConnection $resourceConnection) {}

    public function execute($productId): string
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, [ProductInterface::TYPE_ID])
            ->where('entity_id = ?', $productId);

        $result = $connection->fetchOne($select);

        return $result ?: '';
    }
}
