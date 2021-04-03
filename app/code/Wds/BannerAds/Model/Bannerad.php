<?php
namespace Wds\BannerAds\Model;

class Bannerad extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\BannerAds\Model\ResourceModel\Bannerad');
    }
}
?>