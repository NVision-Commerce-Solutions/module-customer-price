<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Additional;

use Magento\Framework\Exception\LocalizedException;

class AdditionalDataBuilder
{
    public function __construct(private array $providers) {}

    public function build(array $priceInfo, $productId): array
    {
        $additionalData = [];
        foreach ($this->providers as $name => $provider) {
            if (!$provider instanceof  AdditionalDataProviderInterface) {
                throw new LocalizedException(
                    __("Provider %1 should implements AdditionalDataProviderInterface", get_class($provider))
                );
            }
            $additionalData[$name] = $provider->get($priceInfo, $productId);
        }

        return $additionalData;
    }
}
