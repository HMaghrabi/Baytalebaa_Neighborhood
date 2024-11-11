<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Schema upgrade script for customer neighborhood module
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    private const COLUMN_NAME = 'neighborhood';
    private const COLUMN_LENGTH = 255;

    private const TABLES = [
        'customer_address_entity',
        'sales_order_address'
    ];

    /**
     * Upgrades DB schema for the neighborhood module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ): void {
        $setup->startSetup();

        try {
            $this->addNeighborhoodColumns($setup);
        } finally {
            $setup->endSetup();
        }
    }

    /**
     * Add neighborhood column to relevant tables
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addNeighborhoodColumns(SchemaSetupInterface $setup): void
    {
        $connection = $setup->getConnection();

        foreach (self::TABLES as $tableName) {
            $table = $setup->getTable($tableName);

            if (!$connection->tableColumnExists($table, self::COLUMN_NAME)) {
                $this->addNeighborhoodColumn($setup, $table);
            }
        }
    }

    /**
     * Add neighborhood column to specified table
     *
     * @param SchemaSetupInterface $setup
     * @param string $table
     * @return void
     */
    private function addNeighborhoodColumn(SchemaSetupInterface $setup, string $table): void
    {
        $setup->getConnection()->addColumn(
            $table,
            self::COLUMN_NAME,
            [
                'type' => Table::TYPE_TEXT,
                'length' => self::COLUMN_LENGTH,
                'nullable' => true,
                'comment' => 'Neighborhood',
            ]
        );
    }
}