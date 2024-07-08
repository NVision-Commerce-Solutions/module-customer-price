<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Observer;

use Commerce365\CustomerPrice\Model\Command\CleanCache;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class FlushAllCache implements ObserverInterface
{
    public function __construct(private readonly CleanCache $cleanCache) {}

    public function execute(Observer $observer): void
    {
        $this->cleanCache->execute();
    }
}
