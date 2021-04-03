<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Block\Adminhtml\CategoryFilter\Edit;

use Magento\Backend\Block\Widget\Form\Generic as GenericForm;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\Yesno as BooleanOptions;
use MageWorx\SeoExtended\Controller\RegistryConstants;

class Form extends GenericForm
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var BooleanOptions
     */
    protected $booleanOptions;

    /**
     * @var \MageWorx\SeoAll\Model\Source\Category
     */
    protected $categoryOptions;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    /**
     * @var \MageWorx\SeoAll\Helper\Adapter
     */
    protected $helperAdapter;

    /**
     * Form constructor.
     * @param \MageWorx\SeoAll\Model\Source\Category $categoryOptions
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \MageWorx\SeoAll\Helper\Adapter $helperAdapter
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \MageWorx\SeoAll\Model\Source\Category $categoryOptions,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \MageWorx\SeoAll\Helper\Adapter $helperAdapter,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->categoryOptions = $categoryOptions;
        $this->systemStore = $systemStore;
        $this->helperAdapter = $helperAdapter;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \MageWorx\SeoExtended\Api\Data\CategoryFilterInterface */
        $categoryFilter = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CATEGORY_FILTER_CONSTANT);

        $categoryFilterData = $this->_session->getData('mageworx_seoextended_categoryfilter_data', true);
       
        if($categoryFilter->getAttributeOptionId()){
                $attributeOptionId = explode(",", $categoryFilter->getAttributeOptionId());
                 $option_label= $this->getOptionLabel($attributeOptionId);
        }else{
              $attributeOptionId = $this->_request->getParam('attribute_option_id');
                $option_label= $this->getOptionLabel($attributeOptionId);
        }

        if ($categoryFilterData) {
            $categoryFilter->addData($categoryFilterData);
        } else {
            if (!$categoryFilter->getId()) {
                $categoryFilter->addData($categoryFilter->getDefaultValues());
            }
        }
        $data = $this->prepareDataForForm($categoryFilter);

      
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setUseContainer(true);
        $form->setHtmlIdPrefix('categoryfilter_');
        $form->setFieldNameSuffix('categoryfilter');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('SEO Category Filter Info'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($categoryFilter->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'attribute_name',
            'label',
            [
                'label'   => __('URL Key'),
                'value' => $option_label
                
            ]
        );

        $fieldset->addField(
            'attribute_id',
            'hidden',
            [
                'name'     => 'attribute_id'
                            ]
        );
        $fieldset->addField(
            'attribute_option_id',
            'hidden',
            [
                'name'     => 'attribute_option_id',
            ]
        );
        

        if ($categoryFilter->getId()) {
            $fieldset->addField(
                'category_path_names',
                'label',
                [
                    'label'   => __('Category Name'),
                    'value'   => $categoryFilter->getCategoryPathNames()
                ]
            );
        } else {
            $fieldset->addType('mageworx_select', '\MageWorx\SeoAll\Model\Form\Element\Select');

            $fieldset->addField(
                'category_name',
                'mageworx_select',
                [
                    'name'     => 'category_id',
                    'label'    => __('Category Name'),
                    'values'   => $this->categoryOptions->toOptionArray($categoryFilter->getMarkedCategories()),
                    'required' => true,
                ]
            );
        }

        $fieldset->addField(
            'store_id',
            'hidden',
            [
                'name'      => 'store_id',
            ]
        );

        $fieldset->addField(
            'store_name',
            'label',
            [
                'label'   => __('Store View Name'),
                'value'   => $this->systemStore->getStoreName($data['store_id'])
            ]
        );


        $fieldset->addField(
            'meta_title',
            'text',
            [
                'name'      => 'meta_title',
                'label'     => __('Meta Title'),
                'title'     => __('Meta Title'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'meta_description',
            'text',
            [
                'name'      => 'meta_description',
                'label'     => __('Meta Description'),
                'title'     => __('Meta Description'),
                'note'      => $this->getNotice()
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'text',
            [
                'name'      => 'meta_keywords',
                'label'     => __('Meta Keywords'),
                'title'     => __('Meta Keywords'),
            ]
        );
        
        $fieldset->addField(
            'heading',
            'text',
            [
                'name'      => 'heading',
                'label'     => __('Heading'),
                'title'     => __('Heading'),
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name'      => 'description',
                'label'     => __('Description'),
                'title'     => __('Description'),
                'config'    => $this->wysiwygConfig->getConfig()
            ]
        );


        $form->addValues($data);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @param $categoryFilter
     * @return array
     */
    protected function prepareDataForForm($categoryFilter)
    {
        $data = $categoryFilter->getData();
        
        if ($attributeId = (int)$this->_request->getParam('attribute_id')) {
            $data['attribute_id'] = $attributeId;
        }
        
        // MParmar get Multiselect user selection  
        $attributeOptionId = $this->_request->getParam('attribute_option_id');
        if(count($attributeOptionId)){
            $data['attribute_option_id'] = implode(',',$this->_request->getParam('attribute_option_id'));
        }
       
        return $data;
    }

    
    public function getOptionLabel($attributeOptionId){
        
        $option_label = "";
        if(count($attributeOptionId)){
            //KM customize for option label
            $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
                $attributeSetCollection = $objectManager->create('Magento\Eav\Model\Config')
                                            ->getEntityType('catalog_product')
                                            ->getAttributeCollection()
                                            ->addSetInfo();
             $option_label1=array();  
            foreach ($attributeSetCollection as $id=>$attributeSet) {
                
                if ($attributeSet->usesSource() && $attributeSet->getIsFilterable()){
                    if($attributeSet->getFrontendLabel()){
                        
                        $option_label=array();    
                        foreach ($attributeSet->getSource()->getAllOptions() as $option)
                        {
                            if(in_array($option['value'], $attributeOptionId)){
                                if($option['value'])
                                  
                                $option_label1[]=$option['label'];
                            } 
                        }
                    }
                }   
            }
            $option_labelarray = array();
            if(count($option_label1)){
                $option_label = implode(', ',$option_label1);
            }
        } 
        return $option_label;
    }

   
    /**
     * @return string
     */
    protected function getNotice()
    {
        if ($this->helperAdapter->isSeoXTemplatesAvailable()) {
            $msg = __('Dynamic variables and randomizer are available.');
            $msg .= '<br>';
            $msg .= __('See the description on the SEO XTemplates Category Filter Edit Page');

            $msg .= ' ' . '(<i>' . __('Marketing');
            $msg .= ' > ' . __('MageWorx SEO Templates');
            $msg .= ' > ' . __('Category Filter Templates');
            $msg .= '</i>)';

            return $msg;
        }
    }
}
