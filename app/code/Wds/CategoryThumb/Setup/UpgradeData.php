<?php
namespace Wds\CategoryThumb\Setup;
 
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class UpgradeData implements UpgradeDataInterface
{
 
	public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
	{
			$this->eavSetupFactory = $eavSetupFactory;
	}
	 
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
			$setup->startSetup();
	 
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
	 
				$eavSetup->addAttribute(
					\Magento\Catalog\Model\Category::ENTITY,
					'image_thumb', [
						'type'      	=> 'varchar',
						'label'      	=> 'Image - Thumb',
						'input'     	=> 'image',
						'required' 	=> false,
						'sort_order'  => 6,
						'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
						'global'    	=> \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
						'group'    	=> 'General Information',
					]
				);
			$setup->endSetup();
	}
}
 
?>