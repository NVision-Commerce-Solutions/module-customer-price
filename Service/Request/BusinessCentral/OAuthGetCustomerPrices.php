<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Service\Request\BusinessCentral;

use Commerce365\Core\Service\Request\BusinessCentral\OAuthPost as Post;
use Commerce365\CustomerPrice\Service\Customer\CurrencyResolver;
use Commerce365\CustomerPrice\Service\Request\GetCustomerPricesInterface;

class OAuthGetCustomerPrices implements GetCustomerPricesInterface
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
        $priceData = $this->post->execute('GetPrices_GetPricesById', [
            'json' => [
                'customerId' => 1,
                'productIds' => array_values(array_map('intval', $productIds)),
                'currencyCode' => $this->currencyResolver->resolve($customerId),
            ],
            'allow_redirects'=> ['strict' => true]
        ]);

        return !empty($priceData['priceLists']) ? $priceData['priceLists'] : [];
    }
}
