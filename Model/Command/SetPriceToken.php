<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Magento\Framework\App\ResourceConnection;

class SetPriceToken
{
    public const TABLE_NAME = 'commerce365_customer_price_token';

    private ResourceConnection $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $token
     * @param $customerId
     * @return bool
     */
    public function execute(string $token, $customerId): bool
    {
        $connection = $this->resourceConnection->getConnection();

        $dataToInsert = [
            'customer_id' => $customerId,
            'token' => $token
        ];

        $tableName = $connection->getTableName(self::TABLE_NAME);
        $this->resourceConnection->getConnection()->insertOnDuplicate($tableName, $dataToInsert);
        return true;
    }
}
