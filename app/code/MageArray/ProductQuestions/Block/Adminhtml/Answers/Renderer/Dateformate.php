<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Dateformate extends AbstractRenderer
{
    
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    public function render(DataObject $row)
    {
        $date = $this->_getValue($row);
        $value = date('M j Y g:i A', strtotime($date));
        return $value;
    }
}