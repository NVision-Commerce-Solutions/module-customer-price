<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Magento\Framework\App\ResourceConnection;

class GetTokenByCustomerId
{
    public const TABLE_NAME = 'commerce365_customer_price_token';

    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute($customerId): ?string
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName(self::TABLE_NAME);
        $select = $connection->select()
            ->from($tableName, ['token'])
            ->where('customer_id = ?', $customerId)
            ->where('valid_to >= NOW()');

        $token = $connection->fetchOne($select);

        return $token ?: null;
    }
}
