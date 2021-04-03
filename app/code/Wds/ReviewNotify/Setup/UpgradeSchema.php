<?php

namespace Wds\ReviewNotify\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){
        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'email',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Email address'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'company',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Company'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'city',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Location City'
            ]
        );


        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'state',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Location State'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'country',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Location Country'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'experts',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Experts ID'
            ]
        );
        
        $installer->getConnection()->addColumn(
            $installer->getTable('review_detail'),
            'message',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 1000,
                'nullable' => true,
                'comment' => 'Expert Comment'
            ]
        );
      }
    $installer->endSetup();
    }
}
