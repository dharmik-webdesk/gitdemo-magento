<?php

namespace Wds\CategoryLanding\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '1.0.0') < 0){





		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_heading');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'category_heading', [
                        'type' => 'varchar',
                        'label' => 'Heading',
                        'input' => 'text',
						'required' => false,
                        'sort_order' => 110,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'group' => 'Category Landing Page Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'left_box_image');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'left_box_image', [
                        'type' => 'varchar',
                        'label' => 'Left Box Image',
                        'input' => 'image',
                        'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
						'required' => false,
                        'sort_order' => 120,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'group' => 'Category Landing Page Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'left_box_description');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'left_box_description', [
                        'type' => 'text',
                        'label' => 'Left Box Description',
                        'input' => 'textarea',
						'required' => false,
                        'sort_order' => 130,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'wysiwyg_enabled' => false,
                        'is_html_allowed_on_front' => false,
                        'group' => 'Category Landing Page Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'right_box_image');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'right_box_image', [
                        'type' => 'varchar',
                        'label' => 'Right Box Image',
                        'input' => 'image',
                        'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
						'required' => false,
                        'sort_order' => 140,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'group' => 'Category Landing Page Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	

		$eavSetup -> removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'right_box_description');

		
			$eavSetup -> addAttribute(\Magento\Catalog\Model\Category :: ENTITY, 'right_box_description', [
                        'type' => 'text',
                        'label' => 'Right Box Description',
                        'input' => 'textarea',
						'required' => false,
                        'sort_order' => 150,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                        'wysiwyg_enabled' => false,
                        'is_html_allowed_on_front' => false,
                        'group' => 'Category Landing Page Information',
						"default" => "",
						"class"    => "",
						"note"       => ""
			]
			);
					
	
	



		}

    }
}