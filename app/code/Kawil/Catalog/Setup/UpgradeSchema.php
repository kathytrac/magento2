<?php
// @codingStandardsIgnoreFile
namespace Kawil\Catalog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $table = $setup->getTable('catalog_product_entity_tier_price');
            if ($setup->getConnection()->isTableExists($table) == true) {
                $connection->addColumn($table,
                    'cost',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '12,4',
                        null,
                        'comment' => 'COST',
                        'after' => 'website_id',
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
