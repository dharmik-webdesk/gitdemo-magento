<?php

namespace MageArray\ProductQuestions\Model;

use Magento\Framework\Model\AbstractModel;

class Questions extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('MageArray\ProductQuestions\Model\ResourceModel\Questions');
    }

}
