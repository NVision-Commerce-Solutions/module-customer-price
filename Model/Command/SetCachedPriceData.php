<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\ResourceConnection;

class SetCachedPriceData
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

    public function execute(array $dataToInsert): bool
    {
        if (!$this->config->isCachingEnabled()) {
            return true;
        }

        if (empty($dataToInsert)) {
            return true;
        }

        $tableName = $this->resourceConnection->getTableName(CachedPrice::TABLE_NAME);
        $this->resourceConnection->getConnection()->insertOnDuplicate($tableName, $dataToInsert);
        return true;
    }
}
