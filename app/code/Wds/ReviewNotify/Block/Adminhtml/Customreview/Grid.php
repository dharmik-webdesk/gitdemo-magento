<?php
namespace Wds\ReviewNotify\Block\Adminhtml\Customreview;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Wds\ReviewNotify\Model\reviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var \Wds\ReviewNotify\Model\Status
     */
    protected $_status;
    protected $systemStore;
    protected $CollectionFactory;
    protected $_eavAttribute;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Wds\ReviewNotify\Model\reviewFactory $reviewFactory
     * @param \Wds\ReviewNotify\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Wds\ReviewNotify\Model\CustomreviewFactory $ReviewFactory,
        \Wds\ReviewNotify\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
	    \Magento\Store\Model\System\Store $systemStore,
        \Wds\ReviewNotify\Model\OptionsCateogry $OptionsCateogry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        array $data = []
    ) {
        $this->_reviewFactory = $ReviewFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        $this->_systemStore = $systemStore;
        $this->_optionsCateogry = $OptionsCateogry;
        $this->_eavAttribute = $eavAttribute;

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
        $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', 'name');
        
        $collection = $this->_reviewFactory->create()->getCollection();
        
        $collection->getSelect()->join(
            ['catalog_product_entity_varchar' => 'catalog_product_entity_varchar'],
            'main_table.product_id = catalog_product_entity_varchar.entity_id AND catalog_product_entity_varchar.store_id=0 AND catalog_product_entity_varchar.attribute_id='.(int)$attributeId,
            ['product_name' => 'value']
        );


        $collection->getSelect()->join(
        ['secondTable' => 'admin_user'],
        'main_table.admin_id = secondTable.user_id',
        [ "fullname"=> "CONCAT(`secondTable`.`firstname`,' ',`secondTable`.`lastname`)"]
        );


        

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    protected function _filterProductCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where("`catalog_product_entity_varchar`.`value` like ?", "%$value%");
        return $this;
    }
    protected function _filterNameCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->getSelect()->where("CONCAT(`secondTable`.`firstname`,' ',`secondTable`.`lastname`) like ?", "%$value%");
        
        //echo $this->getCollection()->getSelect();
        //exit;
        //$this->getCollection()->_filterNameCondition($value);
        //$this->getCollection()->getSelect();
        //exit;
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
                            'customer_name',
                            [
                                'header' => __('Customer Name'),
                                'index' => 'customer_name',
                                'type' => 'text',
                            ]
                        );
                        
                        $this->addColumn(
                            'customer_email',
                            [
                                'header' => __('Customer Email'),
                                'index' => 'customer_email',
                                'type' => 'text',
                            ]
                        );
                        


		
						
						$this->addColumn(
							'product_name',
							[
								'header' => __('Product Name'),
								'index' => 'product_name',
								'type' => 'text',
                                'filter_condition_callback' => array($this,'_filterProductCondition')
							]
						);
						
                        $this->addColumn(
                            'fullname',
                            [
                                'header' => __('Added By'),
                                'index' => 'fullname',
                                'type' => 'text',
                                'filter_condition_callback' => array($this,'_filterNameCondition')
                            ]
                        );

						$this->addColumn(
							'status',
							[
								'header' => __('Status'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Wds\ReviewNotify\Block\Adminhtml\Customreview\Grid::getOptionArray8()
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
		

		
		   $this->addExportType($this->getUrl('reviewnotify/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('reviewnotify/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Wds_ReviewNotify::review/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('customreview');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('reviewnotify/*/massDelete'),
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
        return $this->getUrl('reviewnotify/*/index', ['_current' => true]);
    }

    /**
     * @param \Wds\ReviewNotify\Model\review|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'reviewnotify/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	
		static public function getOptionArray1()
		{
            $data_array=array(); 
			$data_array[0]='Store 1';
			$data_array[1]='Store 2';
            return($data_array);
		}
		static public function getValueArray1()
		{
            $data_array=array();
			foreach(\Wds\ReviewNotify\Block\Adminhtml\Customreview\Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getChecoutOption(){
			$option[0]='No';
			$option[1]='Yes';			
            return($option);
		}

      
		static public function getOptionArray8()
		{
            $data_array=array(); 
			$data_array[0]='Request Sent';
			$data_array[1]='Review Received';
            return($data_array);
		}
		static public function getValueArray8()
		{
            $data_array=array();
			foreach(\Wds\ReviewNotify\Block\Adminhtml\Customreview\Grid::getOptionArray8() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}
