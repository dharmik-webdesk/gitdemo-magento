<?php
namespace Wds\Experts\Model;

class Experts extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\Experts\Model\ResourceModel\Experts');
    }
}
?>