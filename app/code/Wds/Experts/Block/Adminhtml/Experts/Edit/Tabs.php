<?php
namespace Wds\Experts\Block\Adminhtml\Experts\Edit;

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
        $this->setId('experts_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Expert Information'));
    }
}