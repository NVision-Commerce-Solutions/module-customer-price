<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request;

use Commerce365\Core\Service\Request\Post;
use Magento\Store\Model\StoreManagerInterface;

class GetCustomerPrices
{
    private Post $post;
    private StoreManagerInterface $storeManager;

    /**
     * @param Post $post
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Post $post,
        StoreManagerInterface $storeManager
    ) {
        $this->post = $post;
        $this->storeManager = $storeManager;
    }

    public function execute($productIds, $customerId)
    {
        $priceData = $this->post->execute('price', [
            'json' => [
                'CustomerId' => (int) $customerId,
                'ProductIds' => array_values(array_map('intval', $productIds)),
                'CurrencyCode' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
                'UnitofMeasureCode' => ''
            ],
            'allow_redirects'=> ['strict' => true]
        ]);

        return !empty($priceData['priceLists']) ? $priceData['priceLists'] : [];
    }
}
