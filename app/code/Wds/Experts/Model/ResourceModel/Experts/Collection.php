<?php

namespace Wds\Experts\Model\ResourceModel\Experts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\Experts\Model\Experts', 'Wds\Experts\Model\ResourceModel\Experts');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>