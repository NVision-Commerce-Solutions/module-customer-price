<?php
namespace Commerce365\CustomerPrice\Block\Adminhtml\Cache;

use Magento\Backend\Block\Template;

/**
 * @api
 * @since 100.0.2
 */
class FlushButton extends Template
{
    /**
     * @return string
     */
    public function getFlushCustomerPriceCacheUrl(): string
    {
        return $this->getUrl('commerce365_customerprice/cache/flush');
    }
}
