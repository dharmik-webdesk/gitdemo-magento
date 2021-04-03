<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Questions;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_questionFactory;

    protected $_coreRegistry;

    protected $_status;

    protected $_resource;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Helper\Data $backendHelper,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \MageArray\ProductQuestions\Model\Status $status,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {

        $this->_questionFactory = $questionsFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_status = $status;
        $this->_resource = $resource;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {

        parent::_construct();
        $this->setId('questionsGrid');
        $this->setDefaultSort('product_questions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    protected function _prepareCollection()
    {
        $collection = $this->_questionFactory->create()->getCollection();
        $varcharTable = $this->_resource
            ->getTableName('catalog_product_entity_varchar');
        $eavEntityTable = $this->_resource->getTableName('eav_entity_type');
        $eavAttributeTable = $this->_resource->getTableName('eav_attribute');
        $query = $this->_resource->getConnection()
            ->query(
                "SELECT * FROM $eavEntityTable WHERE entity_type_code='catalog_product'"
            );
        $row = $query->fetch();
        $result = $this->_resource->getConnection()
            ->query(
                "SELECT * FROM $eavAttributeTable WHERE attribute_code='name' and entity_type_id=" . $row['entity_type_id']
            );
        $row = $result->fetch();

        $collection->getSelect()->joinLeft(
            ['pn' => $varcharTable],
            'pn.entity_id = product_id',
            ['pn.value']);
        $collection->getSelect()
            ->where('attribute_id =?', $row['attribute_id']);
            // ->where('pn.value =?', 0);
            
      //   echo $collection->getSelect();
      //   exit;
$collection->getSelect()->group('product_questions_id');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'product_questions_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'product_questions_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'width' => '10px',
            ]
        );

        $this->addColumn(
            'author_name',
            [
                'header' => __('Author Name'),
                'index' => 'author_name',
                'class' => 'xxx',
                'width' => '110px',
            ]
        );

        $this->addColumn(
            'author_email',
            [
                'header' => __('Author Email'),
                'index' => 'author_email',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'date',
                'renderer' => '\MageArray\ProductQuestions\Block\Adminhtml\Questions\Renderer\Dateformate',
                'width' => '150px',
            ]
        );

        $this->addColumn(
            'product_id',
            [
                'header' => __('Product'),
                'index' => 'product_id',
                'renderer' => '\MageArray\ProductQuestions\Block\Adminhtml\Questions\Renderer\Productname',
                'width' => '100px',
                'filter_index' => 'value',
            ]
        );

        $this->addColumn(
            'questions',
            [
                'header' => __('Questions'),
                'index' => 'questions',
                'class' => 'xxx',
                'width' => '300px',
            ]
        );

        $this->addColumn(
            'asked_from',
            [
                'header' => __('Asked By'),
                'index' => 'asked_from',
                'type' => 'options',
                'options' => [
                    '' => ' ',
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
                        'field' => 'product_questions_id'
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
        return $this->getUrl(
            '*/*/edit',
            ['product_questions_id' => $row->getId()]
        );
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('questions');
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
