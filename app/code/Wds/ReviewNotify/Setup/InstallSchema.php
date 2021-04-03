<?php

namespace Wds\ReviewNotify\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

  		$installer->run("CREATE TABLE IF NOT EXISTS `wds_offline_review` (
    `id` int(11) NOT NULL,
    `review_id`int(11) NOT NULL,
    `store_id` varchar(255) NOT NULL DEFAULT '0',
    `customer_name` varchar(255) DEFAULT NULL,
    `customer_email` varchar(255) DEFAULT NULL,
    `message` text DEFAULT NULL,
    `product_id` int(11) NOT NULL DEFAULT '0',
    `status` tinyint(4) NOT NULL DEFAULT '0',
    `is_send_email` tinyint(4) NOT NULL DEFAULT '0',
    `admin_id` tinyint(4) NOT NULL DEFAULT '0',
    `dt` datetime NOT NULL,
    `mo_dt` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

    $installer->run('ALTER TABLE `wds_offline_review`
    ADD PRIMARY KEY (`id`)');

    $installer->run('ALTER TABLE `wds_offline_review`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');


     $installer->run("CREATE TABLE `wds_review_expert_reply` (
    `id` int(11) NOT NULL,
    `review_id` int(11) NOT NULL DEFAULT '0',
    `review_detail_id` int(11) NOT NULL DEFAULT '0',
    `product_id` int(11) NOT NULL DEFAULT '0',
    `message` text DEFAULT NULL,
    `admin_id` tinyint(4) NOT NULL DEFAULT '0',
    `dt` datetime NOT NULL,
    `mo_dt` datetime NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1");

    $installer->run('ALTER TABLE `wds_review_expert_reply`
    ADD PRIMARY KEY (`id`)');

    $installer->run('ALTER TABLE `wds_review_expert_reply`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');


    $installer->run("CREATE TABLE `wds_offline_review_reminder` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_magento_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `status` text COMMENT '1=send, 0= not send',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $installer->run('ALTER TABLE `wds_offline_review_reminder`
  ADD PRIMARY KEY (`id`)');

    $installer->run('ALTER TABLE `wds_offline_review_reminder`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');


      }


   


        $installer->endSetup();

    }
}
