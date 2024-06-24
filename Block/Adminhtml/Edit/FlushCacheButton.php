<?php

namespace Commerce365\CustomerPrice\Block\Adminhtml\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class FlushCacheButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId) {
            $data = [
                'label' => __('Flush Price Cache'),
                'on_click' => sprintf("location.href = '%s';", $this->getFlushCacheUrl()),
                'class' => 'add',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getFlushCacheUrl(): string
    {
        return $this->getUrl('commerce365_customerprice/cache/flushCustomer', ['customer_id' => $this->getCustomerId()]);
    }
}
