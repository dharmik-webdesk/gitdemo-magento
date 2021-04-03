<?php
namespace Wds\ReviewNotify\Model;

class Customreview extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\ReviewNotify\Model\ResourceModel\Customreview');
    }
}
?>