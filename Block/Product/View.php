<?php

namespace Commerce365\CustomerPrice\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{
    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();
        $tierPrices = [];
        $priceInfo = $product->getPriceInfo();
        $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPriceData = [
                'qty' => $tierPrice['price_qty'],
                'price' => $tierPrice['price']->getValue(),
                'basePrice' => $tierPrice['price']->getBaseAmount()
            ];
            $tierPrices[] = $tierPriceData;
        }

        $config = [
            'productId'   => (int)$product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices'      => [
                'baseOldPrice' => [
                    'amount'      => $priceInfo->getPrice('regular_price')->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'oldPrice'   => [
                    //Fixed cast to type
                    'amount'      => (int) $priceInfo->getPrice('regular_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ],
                'basePrice'  => [
                    'amount'      => $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'finalPrice' => [
                    //Fixed cast to type
                    'amount'      => (int) $priceInfo->getPrice('final_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ],
                'tierPrices' => $tierPrices
            ],
            'idSuffix'    => '_clone',
            'tierPrices'  => $tierPrices
        ];

        if (!$this->hasOptions()) {
            return $this->_jsonEncoder->encode($config);
        }

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }
}
