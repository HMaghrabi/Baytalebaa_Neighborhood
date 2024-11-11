<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Data Patch to add neighborhood attribute to customer address
 */
class Neighborhood implements DataPatchInterface
{
    private const ATTRIBUTE_CODE = 'neighborhood';
    private const ENTITY_TYPE = 'customer_address';

    private const FORMS = [
        'adminhtml_customer_address',
        'adminhtml_customer',
        'customer_address_edit',
        'customer_register_address',
        'customer_address',
        'checkout_register',
        'adminhtml_checkout'
    ];

    private CustomerSetupFactory $customerSetupFactory;
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Add neighborhood attribute to customer address
     *
     * @return void
     */
    public function apply(): void
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->addNeighborhoodAttribute($customerSetup);
        $this->configureAttributeForms($customerSetup);
    }

    /**
     * Add neighborhood attribute with its configuration
     *
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return void
     */
    private function addNeighborhoodAttribute($customerSetup): void
    {
        $customerSetup->addAttribute(
            self::ENTITY_TYPE,
            self::ATTRIBUTE_CODE,
            [
                'label' => 'Neighborhood',
                'input' => 'text',
                'type' => Table::TYPE_TEXT,
                'source' => '',
                'required' => false,
                'position' => 90,
                'visible' => true,
                'system' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'frontend_input' => 'hidden',
                'backend' => ''
            ]
        );
    }

    /**
     * Configure attribute forms
     *
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return void
     */
    private function configureAttributeForms($customerSetup): void
    {
        $attribute = $customerSetup->getEavConfig()
            ->getAttribute(self::ENTITY_TYPE, self::ATTRIBUTE_CODE)
            ->addData(['used_in_forms' => self::FORMS]);

        $attribute->save();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion(): string
    {
        return '3.0.6';
    }
}