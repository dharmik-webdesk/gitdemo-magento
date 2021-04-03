<?php

namespace Wds\FilterBuilder\Block\Adminhtml\Attrimage\Edit\Tab;

/**
 * Attrimage edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Wds\FilterBuilder\Model\Status
     */
    protected $_status;

    protected $_optionsCateogry;
    protected $_attribute_id;

    

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Wds\FilterBuilder\Model\OptionsCateogry $OptionsCateogry     
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Wds\FilterBuilder\Model\OptionsCateogry $OptionsCateogry,
        \Wds\FilterBuilder\Model\OptionsProduct $OptionsProduct,
        \Wds\FilterBuilder\Model\Status $status,
        \Wds\FilterBuilder\Helper\Data $data_helper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_optionsCateogry = $OptionsCateogry;
        $this->_optionsProduct = $OptionsProduct;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Wds\FilterBuilder\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('attrimage');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
   

        							
        /*$fieldset->addField(
            'store_id',
            'multiselect',
            [
                'label' => __('Store View'),
                'title' => __('Store View'),
                'name' => 'store_ids[]',
                'required' => true,
		        'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled
            ]
        );*/

        $fieldset->addField(
            'option_id',
            'select',
            [
                'label' => __('Attribute Option'),
                'title' => __('Attribute Option'),
                'name' => 'option_id',
                'required' => true,
				'options'=>$this->_optionsCateogry->getOptionArray(),
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'image_url',
            'image',
            [
                'name' => 'image_path',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );              
        
            
						
						

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
}
