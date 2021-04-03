<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Question extends AbstractRenderer
{
    protected $_questionFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_questionFactory = $questionFactory;
        $this->_authorization = $context->getAuthorization();
    }

    public function render(DataObject $row)
    {
        $id = $this->_getValue($row);
        $questionModel = $this->_questionFactory->create();
        $item = $questionModel->load($id);
        return $item['questions'];
    }
}