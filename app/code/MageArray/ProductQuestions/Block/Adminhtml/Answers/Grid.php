<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    protected $_answersFactory;

    protected $_coreRegistry;

    protected $_status;

    protected $_resource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Helper\Data $backendHelper,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \MageArray\ProductQuestions\Model\Status $status,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_answersFactory = $answersFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_status = $status;
        $this->_resource = $resource;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('answersGrid');
        $this->setDefaultSort('answers_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_answersFactory
            ->create()->getCollection();
        $varcharTable = $this->_resource
            ->getTableName('catalog_product_entity_varchar');
        $questionTable = $this->_resource
            ->getTableName('magearray_product_question');
        $eavEntityTable = $this->_resource->getTableName('eav_entity_type');
        $eavAttributeTable = $this->_resource->getTableName('eav_attribute');
        $query = $this->_resource->getConnection()
            ->query("SELECT * FROM $eavEntityTable WHERE entity_type_code='catalog_product'");
        $row = $query->fetch();
        $result = $this->_resource->getConnection()
            ->query("SELECT * FROM $eavAttributeTable WHERE attribute_code='name' and entity_type_id=" . $row['entity_type_id']);
        $row = $result->fetch();

        $collection->getSelect()->joinLeft(
            ['pq' => $questionTable],
            'pq.product_questions_id = main_table.product_questions_id',
            ['pq.questions',0]);

        $collection->getSelect()->joinLeft(
            ['pn' => $varcharTable],
            'pn.entity_id = pq.product_id',
            ['pn.value',0]);
        $collection->getSelect()
            ->where('attribute_id =?', $row['attribute_id']);
           // ->where('pn.value =?', 0)
           // ->where('pn.questions =?', 0); 
      
       $collection->getSelect()->group('answers_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    public function _filterProductByName($collection, $column)
    {
        
        if (!$value = trim($column->getFilter()->getValue())) { 
            return;
        }
        $name= $column->getFilter()->getValue();
        $collection->addFieldToFilter('pq.questions',array('like'=>'%'.$name.'%'));
        return $collection;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'answers_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'answers_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '20px',
            ]
        );
        
        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'date',
                'renderer' => '\MageArray\ProductQuestions\Block\Adminhtml\Answers\Renderer\Dateformate',
                'width' => '150px',
                'filter_index' => 'main_table.created_at'
            ]
        );

        $this->addColumn(
            'author_name',
            [
                'header' => __('Author Name'),
                'index' => 'author_name',
                'class' => 'xxx',
                'width' => '50px',
                'filter_index' => 'main_table.author_name'
            ]
        );

        $this->addColumn(
            'author_email',
            [
                'header' => __('Author Email'),
                'index' => 'author_email',
                'class' => 'xxx',
                'width' => '50px',
                'filter_index' => 'main_table.author_email'
            ]
        );

        $this->addColumn(
            'product_questions_ids',
            [
                'header' => __('Product'),
                'index' => 'product_questions_id',
                'class' => 'xxx',
                'width' => '50px',
                'renderer' => '\MageArray\ProductQuestions\Block\Adminhtml\Answers\Renderer\Productname',
                'filter_index' => 'value',
            ]
        );

        $this->addColumn(
            'questions',
            [
                'header' => __('Question'),
                'index' => 'questions',
                'class' => 'xxx',
                'filter_index' => 'questions',
                'filter_condition_callback' => array($this, '_filterProductByName'),
            ]
        );

        $this->addColumn(
            'answers',
            [
                'header' => __('Answers'),
                'index' => 'answers',
                'class' => 'xxx',
            ]
        );

        $this->addColumn(
            'answers_from',
            [
                'header' => __('Answer By'),
                'index' => 'answers_from',
                'type' => 'options',
                'options' => [
                    '' => ' ', 
                    'Admin' => 'Admin', 
                    'Customer' => 'Customer',
                    'Guest' => 'Guest'
                ],
                'class' => 'xxx',
                'width' => '20px',
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => ['' => ' ', '1' => 'Pending', '2' => 'Approved'],
                'class' => 'xxx',
                'width' => '50px',
                'filter_index' => 'main_table.status'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'status',
                'type' => 'action',
                'getter' => 'getId',
                'class' => 'xxx',
                'width' => '20px',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                        ],
                        'field' => 'answers_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['answers_id' => $row->getId()]);
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('answers');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete', ['' => '']),
            'confirm' => __('Are you sure?')
        ]);

        $statuses = $this->_status->getOptionArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem('status', [
            'label' => __('Change status'),
            'url' => $this->getUrl('*/*/massStatus', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Status'),
                    'values' => $statuses
                ]
            ]
        ]);

        return $this;
    }
}
