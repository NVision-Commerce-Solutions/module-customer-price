<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Mapper;

use Commerce365\CustomerPrice\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime;

class ResponseToDatabaseMapper implements ResponseToDatabaseMapperInterface
{
    private SerializerInterface $serializer;
    private DateTime $dateTime;
    private Config $config;

    /**
     * @param SerializerInterface $serializer
     * @param DateTime $dateTime
     * @param Config $config
     */
    public function __construct(
        SerializerInterface $serializer,
        DateTime $dateTime,
        Config $config
    ) {
        $this->serializer = $serializer;
        $this->dateTime = $dateTime;
        $this->config = $config;
    }

    public function map(array $priceResponse, $customerId)
    {
        if (empty($priceResponse)) {
            return [];
        }

        $dataToInsert = [];
        foreach ($priceResponse as $item) {
            $priceData = $this->getPriceData($item);
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

    private function getPriceData($item)
    {
        $priceData = $tierPrices = [];
        foreach ($item['prices'] as $price) {
            if (!isset($price['price'])) {
                continue;
            }

            if ($price['minimumQuantity'] <= 1) {
                $priceData['price'] = $price['price'];
                if (!empty($price['salesPrice']) && $this->config->useSpecialPrice()) {
                    $priceData['price'] = $price['salesPrice'];
                    $priceData['special_price'] = $price['price'];
                }
            } else {
                $tierPrices[] = [
                    'qty' => $price['minimumQuantity'],
                    'price' => $price['price'],
                ];
            }
        }

        $priceData['tier_prices'] = $tierPrices;

        return $priceData;
    }
}
