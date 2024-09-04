<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Controller\Adminhtml\Cache;

use Commerce365\CustomerPrice\Model\Command\CleanCache;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Flush extends Action
{
    public function __construct(
        private readonly CleanCache $cleanCache,
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $this->cleanCache->execute();
            $this->messageManager->addSuccessMessage(__('The Customer Price cache has been cleaned.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('adminhtml/*');
    }
}
