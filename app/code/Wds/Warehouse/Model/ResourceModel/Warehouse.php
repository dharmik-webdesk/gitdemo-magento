<?php
/**
 * Copyright © 2015 Wds. All rights reserved.
 */
namespace Wds\Warehouse\Model\ResourceModel;

/**
 * Warehouse resource
 */
class Warehouse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('warehouse_warehouse', 'id');
    }

  
}
