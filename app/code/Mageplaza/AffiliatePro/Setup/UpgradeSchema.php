<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AffiliatePro
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AffiliatePro\Setup;

use Magento\Framework\DB\Ddl\Table as DataType;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Mageplaza\AffiliatePro\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $connection = $installer->getConnection();
            if ($installer->tableExists('mageplaza_affiliate_banner')) {
                $connection->dropTable($installer->getTable('mageplaza_affiliate_banner'));
            }
            $table = $connection->newTable($installer->getTable('mageplaza_affiliate_banner'))
                ->addColumn('banner_id', DataType::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,], 'Banner ID')
                ->addColumn('title', DataType::TYPE_TEXT, 255, ['nullable => false'], 'Banner Title')
                ->addColumn('content', DataType::TYPE_TEXT, 255, [], 'Banner Content')
                ->addColumn('link', DataType::TYPE_TEXT, 255, [], 'Banner Link')
                ->addColumn('status', DataType::TYPE_INTEGER, null, ['nullable => false'], 'Banner Status')
                ->addColumn('rel_nofollow', DataType::TYPE_INTEGER, 255, [], 'Banner Rel Nofollow')
                ->addColumn('campaign_id', DataType::TYPE_INTEGER, null, ['nullable => false'], 'Banner Campaign ID')
                ->addColumn('created_at', DataType::TYPE_TIMESTAMP, null, [], 'Banner Created At')
                ->addColumn('updated_at', DataType::TYPE_TIMESTAMP, null, [], 'Banner Updated At')->setComment('Banner Table');

            $connection->createTable($table);
        }

        $installer->endSetup();
    }
}