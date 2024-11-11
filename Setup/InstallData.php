<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Install data script for customer neighborhood attribute
 */
class InstallData implements InstallDataInterface
{
    private const ATTRIBUTE_CODE = 'neighborhood';
    private const ENTITY_TYPE = 'customer_address';

    private const USED_FORMS = [
        'adminhtml_customer_address',
        'customer_address_edit',
        'customer_register_address'
    ];

    private CustomerSetupFactory $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Install neighborhood attribute for customer address
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ): void {
        $setup->startSetup();

        try {
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $this->createNeighborhoodAttribute($customerSetup);
            $this->configureAttributeForms($customerSetup);
        } finally {
            $setup->endSetup();
        }
    }

    /**
     * Create neighborhood attribute with its configuration
     *
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return void
     */
    private function createNeighborhoodAttribute($customerSetup): void
    {
        $customerSetup->addAttribute(
            self::ENTITY_TYPE,
            self::ATTRIBUTE_CODE,
            [
                'type' => 'text',
                'label' => 'Neighborhood',
                'input' => 'text',
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'sort_order' => 85,
                'position' => 85,
                'system' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
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
            ->setData('used_in_forms', self::USED_FORMS);

        $attribute->save();
    }
}