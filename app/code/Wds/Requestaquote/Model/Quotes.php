<?php
namespace Wds\Requestaquote\Model;

class Quotes extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\Requestaquote\Model\ResourceModel\Quotes');
    }
}
?>