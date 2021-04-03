<?php

namespace MageArray\ProductQuestions\Block\Adminhtml\Questions\Edit\Tab;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_coreRegistry = null;

    protected $_productFactory;

    protected $_questionsFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {

        $this->_productFactory = $productFactory;
        $this->_questionsFactory = $questionsFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()
                    ->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()
                        ->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_productFactory
            ->create()->getCollection()->addAttributeToSelect('*');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_products',
            [
                'type' => 'radio',
                'html_name' => 'products_id',
                'required' => true,
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->_getData(
            'grid_url'
        ) ? $this->_getData(
            'grid_url'
        ) : $this->getUrl(
            'productquestions/*/productgrid',
            ['_current' => true]
        );
    }

    protected function _getSelectedProducts()
    {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }
    
    public function getSelectedProducts()
    {
        $tmId = $this->getRequest()->getParam('product_questions_id');
        if (!isset($tmId)) {
            $tmId = 0;
        }

        $quecollection = $this->_questionsFactory->create()->load($tmId);
        $data = $quecollection->getProductId();
        $products = explode(',', $data);

        $proIds = [];

        foreach ($products as $product) {
            $proIds[$product] = ['id' => $product];
        }

        return $proIds;
    }

}
