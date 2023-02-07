<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Magento\Framework\App\ResourceConnection;

class GetCustomerIdByToken
{
    public const TABLE_NAME = 'commerce365_customer_price_token';

    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(string $token): ?string
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName(self::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, ['customer_id'])
            ->where('token = ?', $token);

        $customerId = $connection->fetchOne($select);
        if (!$customerId) {
            return null;
        }

        return $customerId;
    }
}
