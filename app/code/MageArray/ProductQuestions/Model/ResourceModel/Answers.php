<?php

namespace MageArray\ProductQuestions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Answers extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('magearray_product_answer', 'answers_id');
    }
}