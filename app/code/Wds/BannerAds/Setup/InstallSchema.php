<?php

namespace Wds\BannerAds\Setup;

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

		$installer->run('CREATE TABLE `wds_banner_ads` (
  `id` int(11) NOT NULL,
  `store_id` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `show_on_checkout` TINYINT(1) NOT NULL DEFAULT 0,
  `banner_image_main` varchar(255) NOT NULL,
  `banner_main_url` varchar(255) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `banner_url` varchar(255) NOT NULL,
  `banner_image2` varchar(255) NOT NULL,
  `banner_url2` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `dt` datetime NOT NULL,
  `mo_dt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1');

$installer->run('ALTER TABLE `wds_banner_ads`
  ADD PRIMARY KEY (`id`)');
$installer->run('ALTER TABLE `wds_banner_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT');


		//demo 
//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//$scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
//demo 

		}

        $installer->endSetup();

    }
}
