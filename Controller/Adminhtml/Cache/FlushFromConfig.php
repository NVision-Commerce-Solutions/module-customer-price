<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Controller\Adminhtml\Cache;

use Commerce365\CustomerPrice\Model\Command\CleanCache;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class FlushFromConfig extends Action
{
    /**
     * @param CleanCache $cleanCache
     * @param Context $context
     */
    public function __construct(private readonly CleanCache $cleanCache, Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $this->cleanCache->execute();

            return $resultJson->setData(['success' => true]);
        } catch (\Exception $e) {
            return $resultJson->setData(['success' => false, 'error_message' => $e->getMessage()]);
        }
    }
}
