<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request;

use Commerce365\Core\Service\Request\Post;
use Commerce365\CustomerPrice\Service\Customer\CurrencyResolver;

class GetCustomerPrices
{
    private Post $post;
    private CurrencyResolver $currencyResolver;

    /**
     * @param Post $post
     * @param CurrencyResolver $currencyResolver
     */
    public function __construct(
        Post $post,
        CurrencyResolver $currencyResolver
    ) {
        $this->post = $post;
        $this->currencyResolver = $currencyResolver;
    }

    public function execute($productIds, $customerId)
    {
        $priceData = $this->post->execute('price', [
            'json' => [
                'CustomerId' => (int) $customerId,
                'ProductIds' => array_values(array_map('intval', $productIds)),
                'CurrencyCode' => $this->currencyResolver->resolve($customerId),
                'UnitofMeasureCode' => ''
            ],
            'allow_redirects'=> ['strict' => true]
        ]);

        return !empty($priceData['priceLists']) ? $priceData['priceLists'] : [];
    }
}
