<?php
namespace Wds\BannerAds\Block\Adminhtml\Bannerad;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Wds\BannerAds\Model\banneradFactory
     */
    protected $_banneradFactory;

    /**
     * @var \Wds\BannerAds\Model\Status
     */
    protected $_status;
    protected $systemStore;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Wds\BannerAds\Model\banneradFactory $banneradFactory
     * @param \Wds\BannerAds\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Wds\BannerAds\Model\BanneradFactory $BanneradFactory,
        \Wds\BannerAds\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
	\Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_banneradFactory = $BanneradFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        $this->_systemStore = $systemStore;
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
        $collection = $this->_banneradFactory->create()->getCollection();
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
					'store_id',
					[
						'header' => __('Stores'),
						'index' => 'store_id',
						'type' => 'store',
						        'store_all'     => true,
        'store_view'    => true,
        'sortable'      => true,
        'filter_condition_callback' => array($this,
            '_filterStoreCondition'),
					]
				);


		
						
						$this->addColumn(
							'category_id',
							[
								'header' => __('Category'),
								'index' => 'category_id',
								'type' => 'options',
								'options' => \Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray2()
							]
						);
						$this->addColumn(
							'show_on_checkout',
							[
								'header' => __('Show On Checkout'),
								'index' => 'show_on_checkout',
								'type' => 'options',
								'options' => \Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getChecoutOption()
							]
						);
						
						
				$this->addColumn(
					'banner_image_main',
					[
						'header' => __('Fill Banner'),
						'index' => 'banner_image_main',
						'renderer'  => 'Wds\BannerAds\Block\Adminhtml\Bannerad\Edit\Tab\Renderer\Imageblock'
					]
				);

				
				$this->addColumn(
					'banner_image',
					[
						'header' => __('Banner 1'),
						'index' => 'banner_image',
						'renderer'  => 'Wds\BannerAds\Block\Adminhtml\Bannerad\Edit\Tab\Renderer\ImageblockSecond'
					]
				);
				
				$this->addColumn(
					'banner_image2',
					[
						'header' => __('Banner 2'),
						'index' => 'banner_image2',
						'renderer'  => 'Wds\BannerAds\Block\Adminhtml\Bannerad\Edit\Tab\Renderer\ImageblockThired'
					]
				);
				
				
				/*$this->addColumn(
					'display_order',
					[
						'header' => __('Display Order'),
						'index' => 'display_order',
					]
				);
				*/
						
						$this->addColumn(
							'status',
							[
								'header' => __('Status'),
								'index' => 'status',
								'type' => 'options',
								'options' => \Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray8()
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
		

		
		   $this->addExportType($this->getUrl('bannerads/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('bannerads/*/exportExcel', ['_current' => true]),__('Excel XML'));

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
        //$this->getMassactionBlock()->setTemplate('Wds_BannerAds::bannerad/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('bannerad');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('bannerads/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('bannerads/*/massStatus', ['_current' => true]),
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
        return $this->getUrl('bannerads/*/index', ['_current' => true]);
    }

    /**
     * @param \Wds\BannerAds\Model\bannerad|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'bannerads/*/edit',
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
			foreach(\Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getChecoutOption(){
			$option[0]='No';
			$option[1]='Yes';			
            return($option);
		}
		
		static public function getOptionArray2()
		{
            

			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$categoryFactory = $objectManager->get('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
			$categoriesArray = $categoryFactory->create()->addAttributeToSelect('name')
				->addAttributeToSort('path', 'asc')
				->addFieldToFilter('is_active', '1')
				->load()
				->toArray();
			
				$categories[0]='All Category';
				foreach ($categoriesArray as $categoryId => $category) {
				if (isset($category['name'])) {
					$dash='';
					for($i=2;$i<=$category['level'];$i++)
						$dash.='-- ';
						$category['name']=$dash.$category['name'];
						$categories[$categoryId] = $category['name'];
					}
				}
				
	            return($categories);


		}
		static public function getValueArray2()
		{
            $data_array=array();
			foreach(\Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray2() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		
		static public function getOptionArray8()
		{
            $data_array=array(); 
			$data_array[0]='Enable';
			$data_array[1]='Disable';
            return($data_array);
		}
		static public function getValueArray8()
		{
            $data_array=array();
			foreach(\Wds\BannerAds\Block\Adminhtml\Bannerad\Grid::getOptionArray8() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}
