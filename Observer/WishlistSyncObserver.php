<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Observer;

use Commerce365\CustomerPrice\Service\GetPriceCollectionForProducts;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class WishlistSyncObserver implements ObserverInterface
{
    public function __construct(
        private readonly GetPriceCollectionForProducts $getPriceCollectionForProducts,
        private readonly CustomerSession $customerSession
    ) {}

    public function execute(Observer $observer): void
    {
        /** @var Collection $productCollection */
        $productCollection = $observer->getData('product_collection');

        if (!$productCollection || $productCollection->getSize() === 0) {
            return;
        }

        if (!$this->customerSession->isLoggedIn()) {
            return;
        }

        $productIds = $productCollection->getLoadedIds();
        $customerId = (int) $this->customerSession->getCustomerId();

        if ($productIds && $customerId) {
            $this->getPriceCollectionForProducts->execute($productIds, $customerId);
        }
    }
}
