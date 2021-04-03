<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Answers\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('answers_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Answer Information'));
    }
}
