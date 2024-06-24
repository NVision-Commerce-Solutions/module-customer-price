<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service;

use Commerce365\CustomerPrice\Model\Config;
use Commerce365\CustomerPrice\Service\Additional\AdditionalDataBuilder;

class PriceDataBuilder
{
    public function __construct(
        private readonly Config $config,
        private readonly AdditionalDataBuilder $additionalDataBuilder
    ) {}

    public function build(array $responseItem): array
    {
        $priceData = $tierPrices = [];
        foreach ($responseItem['prices'] as $price) {
            if (!isset($price['price'])) {
                continue;
            }

            if ($price['minimumQuantity'] <= 1) {
                $priceData['price'] = $price['price'];
                if (!empty($price['salesPrice']) && $this->config->useSpecialPrice()) {
                    $priceData['price'] = $price['salesPrice'];
                    $priceData['special_price'] = $price['price'];
                }
                $priceData['additional'] = $this->additionalDataBuilder->build($price, $responseItem['productId']);
            } else {
                $tierPrices[] = [
                    'qty' => $price['minimumQuantity'],
                    'price' => $price['price'],
                    'additional' => $this->additionalDataBuilder->build($price, $responseItem['productId'])
                ];
            }
        }

        $priceData['tier_prices'] = $tierPrices;

        return $priceData;
    }
}
