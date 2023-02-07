<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Customer;

use Commerce365\CustomerPrice\Model\Command\SetPriceToken;
use Magento\Framework\Math\Random;

class GenerateToken
{
    private Random $mathRandom;
    private SetPriceToken $setPriceToken;

    /**
     * @param Random $mathRandom
     * @param SetPriceToken $setPriceToken
     */
    public function __construct(Random $mathRandom, SetPriceToken $setPriceToken)
    {
        $this->mathRandom = $mathRandom;
        $this->setPriceToken = $setPriceToken;
    }

    public function execute($customerId): string
    {
        $newToken = $this->mathRandom->getUniqueHash();

        try {
            $this->setPriceToken->execute($newToken, $customerId);
        } catch (\Exception $e) {
            return '';
        }

        return $newToken;
    }
}
