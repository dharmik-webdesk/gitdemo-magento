<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Questions\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Productname extends AbstractRenderer
{
    protected $_productFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_productFactory = $productFactory;
        $this->_authorization = $context->getAuthorization();
    }

    public function render(DataObject $row)
    {
        $id = $this->_getValue($row);
        $productModel = $this->_productFactory->create();
        $item = $productModel->load($id);
        $url = $this->getUrl('catalog/product/edit/id/' . $id);
        $value = '<a href="' . $url . '">' . $item['name'] . '</a>';
        return $value;
    }
}