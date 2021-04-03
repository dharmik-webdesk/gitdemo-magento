<?php
namespace Wds\ReviewNotify\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ReviewSave implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        
        $review = $observer->getEvent()->getDataObject();
        $connection = $this->_resource;

        $state=$review->getState();
        if(empty($state)){
            $state=$review->getRegion();            
        }
        

        $tableName = $connection->getTableName('review_detail');
        $detail = [
            'email' => $review->getEmail(),
            'company' => $review->getCompany(),
            'city' => $review->getCity(),
            'state' => $state,
        ];

        if($review->getExperts()){
            $detail['experts']=$review->getExperts();
        }
        
        if($review->getMessage()){
            $detail['message']=$review->getMessage();
        }

        

        //if (empty($review->getEmail())) return;
        $reviewe_id=$review->getId();
        $select = $connection->getConnection()->select()->from($tableName)->where('review_id = :review_id');
        $detailId = $connection->getConnection()->fetchOne($select, [':review_id' => $review->getId()]);
        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->getConnection()->update($tableName, $detail, $condition);
        } else {
            $detail['store_id'] = $review->getStoreId();
            $detail['customer_id'] = $review->getCustomerId();
            $detail['review_id'] = $review->getId();
            $connection->getConnection()->insert($tableName, $detail);
        }  

        if($review->getTracking()){
            $tableName2 = $connection->getTableName('wds_offline_review');
            $detail2['status']=1;
            $detail2['review_id']=$reviewe_id;
            $condition2 = ["id = ?" => $review->getTracking()];
            $connection->getConnection()->update($tableName2, $detail2, $condition2);
        } 
    }
}