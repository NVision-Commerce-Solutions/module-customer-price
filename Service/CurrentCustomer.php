<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

class CurrentCustomer
{
    private string $currentCustomerId;

    public function setId(string $customerId): void
    {
        $this->currentCustomerId = $customerId;
    }

    /**
     * @return int
     * @throws \RuntimeException
     */
    public function getId(): int
    {
        if ($this->exists()) {
            return (int) $this->currentCustomerId;
        }
        throw new \RuntimeException('Customer is not set on CustomerRegistry.');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->currentCustomerId;
    }
}
