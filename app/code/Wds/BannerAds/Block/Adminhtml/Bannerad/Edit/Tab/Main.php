<?php

namespace Wds\BannerAds\Block\Adminhtml\Bannerad\Edit\Tab;

/**
 * Bannerad edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Wds\BannerAds\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Wds\BannerAds\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
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
        /* @var $model \Wds\BannerAds\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('bannerad');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
   
									
        $fieldset->addField(
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
        );
										
						
        $fieldset->addField(
            'category_id',
            'select',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'name' => 'category_id',
				
                'options' => \Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray2(),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'show_on_checkout',
            'checkbox',
            [
                'label' => __('Show On Checkout'),
                'title' => __('Show On Checkout'),
                'name' => 'show_on_checkout',
				'value'  => '1',
                'onclick'   => 'this.value = this.checked ? 1 : 0;',
	            'data-form-part' => $model->getData('show_on_checkout'),				
				'checked' => ($model->getData('show_on_checkout')==1)?true:false,
   	            'disabled' => $isElementDisabled
            ]
        );
	
$fieldset->addField(
            'banner_image_main',
            'image',
            [
                'name' => 'banner_image_main_img',
                'label' => __('Fill Banner'),
                'title' => __('Fill Banner'),
				
                'disabled' => $isElementDisabled
            ]
        );
						
	  $fieldset->addField(
            'banner_main_url',
            'text',
            [
                'name' => 'banner_main_url',
                'label' => __('Fill Banner URL'),
                'title' => __('Fill Banner URL'),
				
                'disabled' => $isElementDisabled
            ]
        );										

        $fieldset->addField(
            'banner_image',
            'image',
            [
                'name' => 'banner_image_img',
                'label' => __('Banner 1'),
                'title' => __('Banner 1'),
				
                'disabled' => $isElementDisabled
            ]
        );
						
						
        $fieldset->addField(
            'banner_url',
            'text',
            [
                'name' => 'banner_url',
                'label' => __('Url'),
                'title' => __('Url'),
				
                'disabled' => $isElementDisabled
            ]
        );
									

        $fieldset->addField(
            'banner_image2',
            'image',
            [
                'name' => 'banner_image2_img',
                'label' => __('Banner 2'),
                'title' => __('Banner 2'),
				
                'disabled' => $isElementDisabled
            ]
        );
						
						
        $fieldset->addField(
            'banner_url2',
            'text',
            [
                'name' => 'banner_url2',
                'label' => __('Url'),
                'title' => __('Url'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
       /* $fieldset->addField(
            'display_order',
            'text',
            [
                'name' => 'display_order',
                'label' => __('Display Order'),
                'title' => __('Display Order'),
				
                'disabled' => $isElementDisabled
            ]
        );*/
									
						
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
				
                'options' => \Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray8(),
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
