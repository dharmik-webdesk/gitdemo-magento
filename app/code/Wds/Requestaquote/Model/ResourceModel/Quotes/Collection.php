<?php

namespace Wds\Requestaquote\Model\ResourceModel\Quotes;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wds\Requestaquote\Model\Quotes', 'Wds\Requestaquote\Model\ResourceModel\Quotes');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>