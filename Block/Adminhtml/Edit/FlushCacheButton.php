<?php

namespace Commerce365\CustomerPrice\Block\Adminhtml\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class FlushCacheButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
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

    /**
     * Retrieve the Url for creating an order.
     *
     * @return string
     */
    public function getFlushCacheUrl()
    {
        return $this->getUrl('commerce365_customerprice/cache/flushCustomer', ['customer_id' => $this->getCustomerId()]);
    }
}
