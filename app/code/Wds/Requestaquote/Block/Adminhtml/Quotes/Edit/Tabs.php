<?php
namespace Wds\Requestaquote\Block\Adminhtml\Quotes\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotes_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Quotes Information'));
    }
}