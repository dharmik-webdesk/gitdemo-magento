<?php
namespace Wds\Requestaquote\Model\ResourceModel;

class Quotes extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('quotes', 'id');
    }
}
?>