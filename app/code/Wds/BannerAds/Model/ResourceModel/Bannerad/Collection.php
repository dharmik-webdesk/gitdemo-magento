<?php

namespace Wds\BannerAds\Model\ResourceModel\Bannerad;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\BannerAds\Model\Bannerad', 'Wds\BannerAds\Model\ResourceModel\Bannerad');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    public function addStoreFilter($store, $withAdmin = true){
       /* if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }*/
        if (!is_array($store)) {
            $store = array($store);
        }
        $this->addFilter('store_id', array('in' => $store));
        return $this;
    }
}
?>
