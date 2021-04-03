<?php
namespace Wds\Warehouse\Block\Adminhtml;
class Warehouse extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_warehouse';/*block grid.php directory*/
        $this->_blockGroup = 'Wds_Warehouse';
        $this->_headerText = __('Warehouse');
        $this->_addButtonLabel = __('Add Warehouse'); 
        parent::_construct();
		
    }
}
