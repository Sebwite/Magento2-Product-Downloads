<?php
namespace Sebwite\ProductDownloads\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sebwite_product_downloads'),
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'The Magento store id.',
                    'after' => 'product_id'
                ]
            );

            if ($setup->getConnection()->tableColumnExists($setup->getTable('sebwite_product_downloads'), 'store_id')) {
                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('sebwite_product_downloads', 'store_id', 'store', 'store_id'),
                    $setup->getTable('sebwite_product_downloads'),
                    'store_id',
                    $setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }

        $setup->endSetup();
    }
}
