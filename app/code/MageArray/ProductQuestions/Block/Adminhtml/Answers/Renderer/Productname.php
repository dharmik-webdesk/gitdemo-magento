<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Productname extends AbstractRenderer
{
    protected $_productFactory;

    protected $_questionFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionFactory,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_productFactory = $productFactory;
        $this->_questionFactory = $questionFactory;
        $this->_authorization = $context->getAuthorization();
    }

    public function render(DataObject $row)
    {
        $id = $this->_getValue($row);

        $queModel = $this->_questionFactory->create();
        $que = $queModel->load($id);
        $productModel = $this->_productFactory->create();
        $item = $productModel->load($que->getProductId());
        $url = $this->getUrl('catalog/product/edit/id/' . $que->getProductId());
        $value = '<a href="' . $url . '">' . $item->getName() . '</a>';
        return $value;
    }
}