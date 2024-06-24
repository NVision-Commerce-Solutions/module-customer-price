<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Model\Command;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheManager;
use Magento\Framework\App\ResourceConnection;

class SetPreparedPriceData
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Config $config
    ) {}

    public function execute(array $data): bool
    {
        if (!$this->config->isCachingEnabled()) {
            return true;
        }

        $tableName = $this->resourceConnection->getTableName(HighLevelCacheManager::TABLE_NAME);
        $this->resourceConnection->getConnection()->insertOnDuplicate($tableName, $data);

        return true;
    }
}
