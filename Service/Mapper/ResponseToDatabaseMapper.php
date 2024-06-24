<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Mapper;

use Commerce365\CustomerPrice\Service\PriceDataBuilder;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime;

class ResponseToDatabaseMapper implements ResponseToDatabaseMapperInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly DateTime $dateTime,
        private readonly PriceDataBuilder $priceDataBuilder
    ) {}

    public function map(array $priceResponse, $customerId): array
    {
        if (empty($priceResponse)) {
            return [];
        }

        $dataToInsert = [];
        foreach ($priceResponse as $item) {
            $priceData = $this->priceDataBuilder->build($item);
            if (empty($priceData)) {
                continue;
            }
            $dataToInsert[] = [
                'product_id' => $item['productId'],
                'customer_id' => $customerId,
                'price_data' => $this->serializer->serialize($priceData),
                'last_updated' => $this->dateTime->formatDate(true)
            ];
        }

        return $dataToInsert;
    }
}
