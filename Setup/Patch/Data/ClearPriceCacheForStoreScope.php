<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Setup\Patch\Data;

use Commerce365\CustomerPrice\Model\CachedPrice;
use Commerce365\CustomerPrice\Service\Cache\HighLevelCacheManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Store-view pricing adds store_id to the price cache tables. Rows cached before
 * this upgrade predate the column (the schema upgrade backfills them to store_id 0)
 * and can no longer be matched by the store-scoped lookups, so we clear the caches
 * once. They repopulate on demand from the per-scope Business Central configuration.
 */
class ClearPriceCacheForStoreScope implements DataPatchInterface
{
    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function apply(): void
    {
        $connection = $this->resourceConnection->getConnection();

        foreach ([CachedPrice::TABLE_NAME, HighLevelCacheManager::TABLE_NAME] as $table) {
            $tableName = $this->resourceConnection->getTableName($table);
            if ($connection->isTableExists($tableName)) {
                $connection->delete($tableName);
            }
        }
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
