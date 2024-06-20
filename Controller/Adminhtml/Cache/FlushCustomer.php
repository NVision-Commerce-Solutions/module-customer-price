<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Controller\Adminhtml\Cache;

use Commerce365\CustomerPrice\Model\Command\CleanCacheCustomer;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class FlushCustomer extends Action
{
    public function __construct(
        private readonly CleanCacheCustomer $cleanCache,
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            try {
                $this->cleanCache->execute($customerId);
                $this->messageManager->addSuccessMessage(__('Cache successfully flushed'));
                $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('customer/index/edit', ['id' => $customerId, '_current' => true]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a customer to flush cache.'));
            $resultRedirect->setPath('customer/index/index');
        }

        return $resultRedirect;
    }
}
