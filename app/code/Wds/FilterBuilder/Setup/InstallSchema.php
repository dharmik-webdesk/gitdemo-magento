<?php

namespace Wds\FilterBuilder\Setup;

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

		$installer->run('CREATE TABLE `wds_attribute_images` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mo_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1');

  $installer->run('ALTER TABLE `wds_attribute_images`
  ADD PRIMARY KEY (`id`)');

  $installer->run('ALTER TABLE `wds_attribute_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');

		}

        $installer->endSetup();

    }
}
