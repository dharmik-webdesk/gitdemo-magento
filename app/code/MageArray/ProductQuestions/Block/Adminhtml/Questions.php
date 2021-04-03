<?php
namespace MageArray\ProductQuestions\Block\Adminhtml;

class Questions extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_questions';
        $this->_blockGroup = 'MageArray_ProductQuestions';
        $this->_headerText = __('Manage Questions');
        $this->_addButtonLabel = __('Add New Question');
        parent::_construct();
    }
}
