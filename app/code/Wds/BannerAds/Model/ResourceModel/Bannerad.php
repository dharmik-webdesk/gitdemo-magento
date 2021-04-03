<?php
namespace Wds\BannerAds\Model\ResourceModel;

class Bannerad extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('wds_banner_ads', 'id');
    }
}
?>