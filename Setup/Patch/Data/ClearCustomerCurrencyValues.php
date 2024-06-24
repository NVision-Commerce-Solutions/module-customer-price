<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class ClearCustomerCurrencyValues implements DataPatchInterface
{
    public function __construct(
        private readonly AttributeRepositoryInterface $attributeRepository,
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function apply(): void
    {
        $attribute = $this->attributeRepository->get(Customer::ENTITY, 'bc_customer_currency');
        $tableName = $this->resourceConnection->getTableName('customer_entity_varchar');
        $this->resourceConnection->getConnection()
            ->delete(
                $tableName,
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
