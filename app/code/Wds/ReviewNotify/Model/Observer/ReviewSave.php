<?php
namespace Wds\ReviewNotify\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ReviewSave implements ObserverInterface
{   
    
    /* skvirja added variables */
    public $_reviewObject;
    public $starArrayValues;
    protected $eavConfig;
    public $_ratingAttributeCode;
    /* end skvirja added variables */

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_resource = $resource;
        $this->eavConfig = $eavConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        
        $review = $observer->getEvent()->getDataObject();
        
        //skvirja updated
        $this->_reviewObject = $review;
        $this->updateReviewAtrribute();
        // end skvirja code 

        
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
    public function updateReviewAtrribute()
    {
        // define created rating attribute code
        $this->_ratingAttributeCode = 'rating';

        $connection = $this->_resource;
        $entity_pk_value = $this->_reviewObject->getEntityPkValue();
        if(isset($entity_pk_value) && $entity_pk_value != null)
        {
            $reviews = array();
            $finalRattingValue = 0;
            $finalRattingOptionValue = 0;
            $tableName = $this->_resource->getTableName('rating_option_vote_aggregated');
            $fields = array('entity_pk_value','percent_approved');
            $select = $this->_resource->getConnection()->select()->from($tableName,$fields)->where('entity_pk_value = ?',$entity_pk_value)->where('store_id = ?',0)->group('entity_pk_value');
            //$detailId = $this->_resource->getConnection()->fetchOne($select, [':entity_pk_value' => $entity_pk_value]);
            $reviews = $this->_resource->getConnection()->fetchRow($select);
            if(isset($reviews) && count($reviews) > 0){
                $reviews_percentage = $reviews['percent_approved'];
                $this->starArrayValues = array(
                    '1'=>array('min'=>0,'max'=>20),
                    '2'=>array('min'=>21,'max'=>40),
                    '3'=>array('min'=>41,'max'=>60),
                    '4'=>array('min'=>61,'max'=>80),
                    '5'=>array('min'=>81,'max'=>100),
                );
                foreach($this->starArrayValues as $rattingNum => $group)
                {
                    if($reviews_percentage >= $group['min'] && $reviews_percentage <= $group['max'])
                    {
                        $finalRattingValue = $rattingNum;
                        break;
                    }
                }

                // get all the rating option for attribute code = rating
                $attribute = $this->eavConfig->getAttribute('catalog_product', $this->_ratingAttributeCode);

                $options = $attribute->getSource()->getAllOptions();
                if(count($options)>0)
                {
                    foreach ($options as $option) {
                        if(isset($option['label']) && $option['label'] == $finalRattingValue)
                        {
                           $finalRattingOptionValue = $option['value']; 
                           break;
                        }
                    }
                }
                if($finalRattingOptionValue == 0)
                {
                    $finalRattingOptionValue = $attribute->getDefaultValue();
                }

                // update rating value onto the products 
                $attributeId = $attribute->getAttributeId();

                $ratingTableName = $this->_resource->getTableName('catalog_product_entity_int');
                $select_attribute = $this->_resource->getConnection()->select()->from($ratingTableName)
                            ->where('attribute_id = ?',$attributeId)
                            ->where('store_id = ?',0)
                            ->where('entity_id = ?',$entity_pk_value);
                $attribute_found = $this->_resource->getConnection()->fetchOne($select_attribute);
                if(isset($attribute_found) && !empty($attribute_found))
                {
                    
                    $detail_update = array();
                    $detail_update['value'] = $finalRattingOptionValue;
                    $condition_update = ["value_id = ?" => $attribute_found];
                    $connection->getConnection()->update($ratingTableName, $detail_update, $condition_update);
                }else
                {
                    
                    $detail_insert = array();
                    $detail_insert['attribute_id'] = $attributeId;
                    //$detail_insert['store_id'] = 0;
                    $detail_insert['entity_id'] = $entity_pk_value;
                    $detail_insert['value'] = $finalRattingOptionValue;
                    $connection->getConnection()->insert($ratingTableName, $detail_insert);

                } 
            } 
        }
    }
}