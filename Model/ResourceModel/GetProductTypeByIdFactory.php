<?php
declare(strict_types=1);
namespace Commerce365\CustomerPrice\Model\ResourceModel;

use Magento\Framework\ObjectManagerInterface;

/**
 * Added for Magento <2.4.5 compatibility
 * \Magento\Catalog\Model\ResourceModel\GetProductTypeById was introduced in version 2.4.5
 */
class GetProductTypeByIdFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create object, first we check if Magento default exists, otherwise we use fallback
     *
     * @param array $data
     * @return mixed|string
     */
    public function create(array $data = array())
    {
        if (class_exists('Magento\Catalog\Model\ResourceModel\GetProductTypeById')) {
            $instanceName = 'Magento\Catalog\Model\ResourceModel\GetProductTypeById';
        } else {
            $instanceName = GetProductTypeById::class;
        }
        return $this->objectManager->create($instanceName, $data);
    }
}
