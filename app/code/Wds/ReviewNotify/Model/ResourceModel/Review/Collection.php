<?php

namespace Wds\ReviewNotify\Model\ResourceModel\Review;

use Magento\Framework\Model\AbstractModel;

/**
 * Review resource model
 */
class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{

	protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(array('detail2' => $this->_reviewDetailTable),
                'main_table.review_id = detail2.review_id',
                array('detail_id', 'title', 'detail', 'nickname', 'email', 'company', 'city', 'country', 'experts', 'message', 'customer_id'));
        
        return $this;
    }


}