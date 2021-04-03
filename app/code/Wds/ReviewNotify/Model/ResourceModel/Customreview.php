<?php
namespace Wds\ReviewNotify\Model\ResourceModel;

class Customreview extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wds_offline_review', 'id');
    }
}
?>