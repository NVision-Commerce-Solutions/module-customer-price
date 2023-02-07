<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Controller\Adminhtml\Cache;

use Commerce365\CustomerPrice\Model\Command\CleanCache;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Flush extends Action
{
    private CleanCache $cleanCache;

    /**
     * @param CleanCache $cleanCache
     * @param Context $context
     */
    public function __construct(CleanCache $cleanCache, Context $context)
    {
        parent::__construct($context);
        $this->cleanCache = $cleanCache;
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
