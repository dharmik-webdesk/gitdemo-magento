<?php
namespace Wds\Warehouse\Block\Adminhtml\Warehouse\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_warehouse_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Warehouse Information'));
    }
}