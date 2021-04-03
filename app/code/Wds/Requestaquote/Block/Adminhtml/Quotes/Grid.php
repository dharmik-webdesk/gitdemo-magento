<?php
namespace Wds\Requestaquote\Block\Adminhtml\Quotes;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Wds\Requestaquote\Model\quotesFactory
     */
    protected $_quotesFactory;

    /**
     * @var \Wds\Requestaquote\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Wds\Requestaquote\Model\quotesFactory $quotesFactory
     * @param \Wds\Requestaquote\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Wds\Requestaquote\Model\QuotesFactory $QuotesFactory,
        \Wds\Requestaquote\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_quotesFactory = $QuotesFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
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
        $collection = $this->_quotesFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
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
					'name',
					[
						'header' => __('Name'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'email',
					[
						'header' => __('Email'),
						'index' => 'email',
					]
				);
				
						
						$this->addColumn(
							'state',
							[
								'header' => __('State'),
								'index' => 'state',
								'type' => 'options',
								'options' => \Wds\Requestaquote\Block\Adminhtml\Quotes\Grid::getOptionArray2()
							]
						);
						
						
				$this->addColumn(
					'subject',
					[
						'header' => __('Subject'),
						'index' => 'subject',
					]
				);
				
				$this->addColumn(
					'subject',
					[
						'header' => __('Product SKU'),
						'index' => 'sku',
					]
				);
		
		   $this->addExportType($this->getUrl('requestaquote/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('requestaquote/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Wds_Requestaquote::quotes/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('quotes');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('requestaquote/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('requestaquote/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('requestaquote/*/index', ['_current' => true]);
    }

    /**
     * @param \Wds\Requestaquote\Model\quotes|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'requestaquote/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	
		static public function getOptionArray2()
		{
            $data_array=array(); 
			$data_array[0]='Alabama';
			$data_array[1]='Alaska';
			$data_array[2]='American Samoa';
			$data_array[3]='Arizona';
			$data_array[4]='Arkansas';
			$data_array[5]='Armed Forces Africa';
			$data_array[6]='Armed Forces Americas';
			$data_array[7]='Armed Forces Canada';
			$data_array[8]='Armed Forces Europe';
			$data_array[9]='Armed Forces Middle East';
			$data_array[10]='Armed Forces Pacific';
			$data_array[11]='California';
			$data_array[12]='Colorado';
			$data_array[13]='Connecticut';
			$data_array[14]='Delaware';
			$data_array[15]='District of Columbia';
			$data_array[16]='Federated States Of Micronesia';
			$data_array[17]='Florida';
			$data_array[18]='Georgia';
			$data_array[19]='Guam';
			$data_array[20]='Hawaii';
			$data_array[21]='Idaho';
			$data_array[22]='Illinois';
			$data_array[23]='Indiana';
			$data_array[24]='Iowa';
			$data_array[25]='Kansas';
			$data_array[26]='Kentucky';
			$data_array[27]='Louisiana';
			$data_array[28]='Maine';
			$data_array[29]='Marshall Islands';
			$data_array[30]='Maryland';
			$data_array[31]='Massachusetts';
			$data_array[32]='Michigan';
			$data_array[33]='Minnesota';
			$data_array[34]='Mississippi';
			$data_array[35]='Missouri';
			$data_array[36]='Montana';
			$data_array[37]='Nebraska';
			$data_array[38]='Nevada';
			$data_array[39]='New Hampshire';
			$data_array[40]='New Jersey';
			$data_array[41]='New Mexico';
			$data_array[42]='New York';
			$data_array[43]='North Carolina';
			$data_array[44]='North Dakota';
			$data_array[45]='Northern Mariana Islands';
			$data_array[46]='Ohio';
			$data_array[47]='Oklahoma';
			$data_array[48]='Oregon';
			$data_array[49]='Palau';
			$data_array[50]='Pennsylvania';
			$data_array[51]='Puerto Rico';
			$data_array[52]='Rhode Island';
			$data_array[53]='South Carolina';
			$data_array[54]='South Dakota';
			$data_array[55]='Tennessee';
			$data_array[56]='Texas';
			$data_array[57]='Utah';
			$data_array[58]='Vermont';
			$data_array[59]='Virgin Islands';
			$data_array[60]='Virginia';
			$data_array[61]='Washington';
			$data_array[62]='West Virginia';
			$data_array[63]='Wisconsin';
			$data_array[64]='Wyoming';
            return($data_array);
		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(\Wds\Requestaquote\Block\Adminhtml\Quotes\Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}