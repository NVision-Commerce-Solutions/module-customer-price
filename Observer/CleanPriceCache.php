<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Observer;

use Commerce365\CustomerPrice\Service\Cache\CleanCache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CleanPriceCache implements ObserverInterface
{
    public function __construct(private readonly CleanCache $cleanCache) {}

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $changedPaths = $observer->getEvent()->getChangedPaths();
        $this->cleanCache->execute($changedPaths);
    }
}
