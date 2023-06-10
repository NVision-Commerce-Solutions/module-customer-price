<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ClearCustomerCurrencyValues implements DataPatchInterface
{
    private AttributeRepositoryInterface $attributeRepository;
    private ResourceConnection $resourceConnection;

    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        ResourceConnection $resourceConnection
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->resourceConnection = $resourceConnection;
    }

    public function apply()
    {
        $attribute = $this->attributeRepository->get(Customer::ENTITY, 'bc_customer_currency');
        $this->resourceConnection->getConnection()
            ->delete(
                'customer_entity_varchar',
                ['attribute_id = ?' => $attribute->getAttributeId()]
            );
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }
}
