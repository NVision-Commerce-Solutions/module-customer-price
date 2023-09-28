<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional;

use RuntimeException;

class AdditionalDataBuilder
{
    private array $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function build(array $priceInfo, $productId): array
    {
        $additionalData = [];
        foreach ($this->providers as $name => $provider) {
            if (!$provider instanceof  AdditionalDataProviderInterface) {
                throw new RuntimeException(
                    __("Provider %1 should implements AdditionalDataProviderInterface", get_class($provider))
                );
            }
            $additionalData[$name] = $provider->get($priceInfo, $productId);
        }

        return $additionalData;
    }
}
