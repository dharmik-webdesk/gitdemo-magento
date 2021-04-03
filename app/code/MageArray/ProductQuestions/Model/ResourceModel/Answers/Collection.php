<?php

namespace MageArray\ProductQuestions\Model\ResourceModel\Answers;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'MageArray\ProductQuestions\Model\Answers',
            'MageArray\ProductQuestions\Model\ResourceModel\Answers'
        );
    }
}