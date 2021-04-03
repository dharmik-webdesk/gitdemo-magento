<?php
namespace Wds\Warehouse\Block\Adminhtml\Warehouse\Edit\Tab;
class WarehouseInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_country;
	protected $_regionFactory;
	protected $_countryFactory;

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
		\Magento\Directory\Model\Config\Source\Country $_country,
		\Magento\Directory\Model\CountryFactory $_countryFactory,
		array $data = array()
    ) {
        $this->_systemStore = $systemStore;
		$this->_country = $_country;
		$this->_countryFactory = $_countryFactory;
		
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('warehouse_warehouse');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Warehouse Information')));
		$fieldset->addType(
            'warehouse_image',
            '\Wds\Warehouse\Block\Adminhtml\Warehouse\Edit\Renderer'
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
		 $fieldset->addField(
            'status',
            'select',
            array(
                'name'      => 'status',
                'label'     => __('Status'),
                'values'   =>  $options = [ 'Enabled' =>'Enabled','Disabled' => 'Disabled'],
            )
        );
		$fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            )
        );
		$fieldset->addField(
            'address_line_01',
            'text',
            array(
                'name' => 'address_line_01',
                'label' => __('Address'),
                'title' => __('Address'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'address_line_02',
            'text',
            array(
                'name' => 'address_line_02',
             )
        );
		$fieldset->addField(
            'zip_code',
            'text',
            array(
                'name' => 'zip_code',
                'label' => __('Zip Code'),
                'title' => __('Zip Code'),
                'required' => true,
            )
        );
		$stateArray = $this->_countryFactory->create()->setId('US')->getLoadedRegionCollection()->toOptionArray();
		
		$fieldset->addField(
			'state',
			'select',
			array('name' => 'state', 'label' => __('State'), 'values' => $stateArray,'required' => true)
		);
		$countries = $this->_country->toOptionArray();
		$fieldset->addField(
			'country',
			'select',
			array('name' => 'country', 'label' => __('Country'), 'values' => $countries,'required' => true)
		);
		
		
		$fieldset->addField(
            'telephone',
            'text',
            array(
                'name' => 'telephone',
                'label' => __('Telephone'),
                'title' => __('Telephone'),
                /*'required' => true,*/
            )
        );
	
		/* $fieldset->addField(
            'Image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
				
                'disabled' => $isElementDisabled
            ]
        ); */
		/*{{CedAddFormField}}*/
		
		
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Warehouse Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Warehouse Information');
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
	
}
