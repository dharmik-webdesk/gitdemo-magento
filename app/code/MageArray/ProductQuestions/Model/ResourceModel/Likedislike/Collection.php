<?php

namespace MageArray\ProductQuestions\Model\ResourceModel\Likedislike;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'MageArray\ProductQuestions\Model\Likedislike',
            'MageArray\ProductQuestions\Model\ResourceModel\Likedislike'
        );
    }
}