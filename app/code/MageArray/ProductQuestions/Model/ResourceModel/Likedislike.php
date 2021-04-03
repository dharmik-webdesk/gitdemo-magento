<?php

namespace MageArray\ProductQuestions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Likedislike extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('magearray_like_dislike', 'like_dislike_id');
    }
}