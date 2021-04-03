<?php

namespace MageArray\ProductQuestions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Questions extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('magearray_product_question', 'product_questions_id');
    }
}