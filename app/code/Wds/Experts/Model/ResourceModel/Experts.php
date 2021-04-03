<?php
namespace Wds\Experts\Model\ResourceModel;

class Experts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('experts', 'id');
    }
}
?>