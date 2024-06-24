<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class CurrentStore
{
    private int $currentStoreId = 0;

    public function __construct(private readonly StoreManagerInterface $storeManager) {}

    public function setId(string $customerId): void
    {
        $this->currentStoreId = (int) $customerId;
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    public function getId(): int
    {
        if (!$this->exists()) {
            $this->currentStoreId = (int) $this->storeManager->getStore()->getId();
        }

        if (!$this->exists()) {
            throw new LocalizedException(__('Current store does not exist.'));
        }

        return $this->currentStoreId;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->currentStoreId;
    }
}
