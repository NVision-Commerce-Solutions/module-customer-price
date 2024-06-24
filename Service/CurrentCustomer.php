<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Magento\Customer\Model\SessionFactory;

class CurrentCustomer
{
    private int $currentCustomerId = 0;

    public function __construct(private readonly SessionFactory $sessionFactory) {}

    public function setId(string $customerId): void
    {
        $this->currentCustomerId = (int) $customerId;
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    public function getId(): int
    {
        if (!$this->exists()) {
            return (int) $this->sessionFactory->create()->getCustomerId();
        }

        return $this->currentCustomerId;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->currentCustomerId;
    }
}
