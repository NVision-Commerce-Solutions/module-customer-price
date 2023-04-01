<?php

declare(strict_types=1);

namespace Commerce365\CustomerPrice\Setup\Patch\Data;

use Commerce365\CustomerPrice\Model\Source\Currency;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomerCurrencyAttribute implements DataPatchInterface
{
    private const ATTRIBUTE_NAME = 'bc_customer_currency';
    private EavSetupFactory $setupFactory;
    private ModuleDataSetupInterface $moduleDataSetup;
    private Config $eavConfig;

    public function __construct(
        EavSetupFactory $setupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        Config $eavConfig
    ) {
        $this->setupFactory = $setupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
    }

    public function getAliases(): array
    {
        return [];
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function apply()
    {
        $eavSetup = $this->setupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            self::ATTRIBUTE_NAME,
            [
                'type' => 'varchar',
                'label' => 'Currency',
                'input' => 'select',
                'required' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'position' => 140,
                'system' => false,
                'visible' => true,
                'source' => Currency::class
            ]
        );

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, self::ATTRIBUTE_NAME);

        $attribute->setData('used_in_forms', ['adminhtml_customer'])->save();

        return $this;
    }
}
