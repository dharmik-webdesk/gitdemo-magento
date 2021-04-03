<?php
namespace MageArray\ProductQuestions\Block\Adminhtml;

class Answers extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_answers';
        $this->_blockGroup = 'MageArray_ProductQuestions';
        $this->_headerText = __('Manage Answers');
        $this->_addButtonLabel = __('Add New Answers');
        parent::_construct();
    }
}
