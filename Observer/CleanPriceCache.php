<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Observer;

use Commerce365\CustomerPrice\Model\Command\CleanCache;
use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CleanPriceCache implements ObserverInterface
{
    private CleanCache $cleanCache;
    private Pool $cachePool;

    /**
     * @param CleanCache $cleanCache
     * @param Pool $cachePool
     */
    public function __construct(CleanCache $cleanCache, Pool $cachePool)
    {
        $this->cleanCache = $cleanCache;
        $this->cachePool = $cachePool;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $changedPaths = $observer->getEvent()->getChangedPaths();
        if (in_array(Config::XML_PATH_SPECIAL_PRICE, $changedPaths)) {
            $this->cleanCache->execute();
        }

        if (in_array(Config::XML_PATH_AJAX_ENABLED, $changedPaths)) {
            foreach ($this->cachePool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
        }
    }
}
