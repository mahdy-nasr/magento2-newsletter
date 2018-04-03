<?php
/**
 * Created by PhpStorm.
 * User: mahdynasr
 * Date: 31/03/18
 */
namespace MahdyNasr\Newsletter\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->getConnection()->addColumn(
            $setup->getTable('newsletter_subscriber'),
            'first_name',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => "subscriber's first name"
            ]
        );

        $setup->endSetup();
    }

}