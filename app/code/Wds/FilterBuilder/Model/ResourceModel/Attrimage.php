<?php
namespace Wds\FilterBuilder\Model\ResourceModel;

class Attrimage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wds_attribute_images', 'id');
    }
}
?>