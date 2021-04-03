<?php

namespace Wds\ReviewNotify\Model\ResourceModel\Customreview;

//class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\ReviewNotify\Model\Customreview', 'Wds\ReviewNotify\Model\ResourceModel\Customreview');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    public function addProductNameFilter($keyword, $withAdmin = true){
            
        $this->addFilter('customer_name',array('like' => '%'.$keyword.'%'));
        return $this;
    }

    public function _filterNameCondition($keyword, $withAdmin = true){
        $this->addFilter("CONCAT(`secondTable`.`firstname`,' ',`secondTable`.`lastname`)",array('like' => '%'.$keyword.'%'));
        return $this;
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
