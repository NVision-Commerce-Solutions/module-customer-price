<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Cache;

use Commerce365\CustomerPrice\Model\Command\CleanCache as CleanCacheCommand;
use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\Cache\Frontend\Pool;

class CleanCache
{
    public function __construct(
        private readonly CleanCacheCommand $cleanCache,
        private readonly Pool $cachePool
    ) {}

    public function execute(array $changedPaths): void
    {
        if (empty($changedPaths)) {
            return;
        }

        if (in_array(Config::XML_PATH_SPECIAL_PRICE, $changedPaths, true)) {
            $this->cleanCache->execute();
        }

        if (in_array(Config::XML_PATH_AJAX_ENABLED, $changedPaths, true)) {
            foreach ($this->cachePool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
        }
    }
}
