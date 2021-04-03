<?php

namespace MageArray\ProductQuestions\Model\ResourceModel\Questions;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'MageArray\ProductQuestions\Model\Questions',
            'MageArray\ProductQuestions\Model\ResourceModel\Questions'
        );
    }

}