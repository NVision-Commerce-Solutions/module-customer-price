<?php

namespace Commerce365\CustomerPrice\Service\Additional;

interface AdditionalDataProviderInterface
{
    public function get(array $priceInfo, $productId): string;
}
