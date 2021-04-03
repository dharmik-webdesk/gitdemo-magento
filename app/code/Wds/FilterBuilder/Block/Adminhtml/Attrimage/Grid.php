<?php
namespace Wds\FilterBuilder\Block\Adminhtml\Attrimage;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Wds\FilterBuilder\Model\attrimageFactory
     */
    protected $_attrimageFactory;

    /**
     * @var \Wds\FilterBuilder\Model\Status
     */
    protected $_status;
    protected $systemStore;
    protected $CollectionFactory;
    


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Wds\FilterBuilder\Model\attrimageFactory $attrimageFactory
     * @param \Wds\FilterBuilder\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Wds\FilterBuilder\Model\AttrimageFactory $AttrimageFactory,
        \Wds\FilterBuilder\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
	    \Magento\Store\Model\System\Store $systemStore,
        \Wds\FilterBuilder\Model\OptionsCateogry $OptionsCateogry,
        array $data = []
    ) {
        $this->_attrimageFactory = $AttrimageFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        $this->_systemStore = $systemStore;
        $this->_optionsCateogry = $OptionsCateogry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_attrimageFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }
    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
                            'image_url',
                            [
                                'header' => __('Image'),
                                'type' => 'text',
                                'renderer'  => '\Wds\FilterBuilder\Block\Adminhtml\Attrimage\Edit\Tab\Renderer\Imageblock',
                            ]
                        );
                        
        
		
						
			$this->addColumn(
				'option_id',
				[
					'header' => __('Attribute Name'),
					'index' => 'option_id',
					'type' => 'options',
					'options' => $this->_optionsCateogry->getOptionArray()
				]
			);
			
						
					
						
						
						


		
        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
		

		
		   //$this->addExportType($this->getUrl('attrimages/*/exportCsv', ['_current' => true]),__('CSV'));
		  // $this->addExportType($this->getUrl('attrimages/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Wds_FilterBuilder::attrimage/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('attrimage');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('attrimages/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('attrimages/*/index', ['_current' => true]);
    }

    /**
     * @param \Wds\FilterBuilder\Model\attrimage|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'attrimages/*/edit',
            ['id' => $row->getId()]
        );
		
    }
    
}
