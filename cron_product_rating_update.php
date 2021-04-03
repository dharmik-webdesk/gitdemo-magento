<?php 
use Magento\Framework\App\Bootstrap;
$path= __DIR__;
require $path.'/app/bootstrap.php';



$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$url = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');


$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('frontend');

$resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
$connection = $resource;
$eavConfig = $objectManager->get('\Magento\Eav\Model\Config');

error_reporting(E_ALL);
ini_set('display_errors',1);
set_time_limit(0);

$reviews = array();
$productIds = array();
$finalRattingValue = 0;
$ratingAttributeCode = 'rating';
$starArrayValues = array(
    '1'=>array('min'=>0,'max'=>20),
    '2'=>array('min'=>21,'max'=>40),
    '3'=>array('min'=>41,'max'=>60),
    '4'=>array('min'=>61,'max'=>80),
    '5'=>array('min'=>81,'max'=>100),
);
// get all the rating option for attribute code = rating
$attribute = $eavConfig->getAttribute('catalog_product', $ratingAttributeCode);
$options = $attribute->getSource()->getAllOptions();
$attributeId = $attribute->getAttributeId();

// get all the reviews
$reivews_tableName = $connection->getTableName('rating_option_vote_aggregated');
$sql_reviews_all = "SELECT entity_pk_value,sum(percent_approved)/COUNT(primary_id) as per FROM ". $reivews_tableName ." group by entity_pk_value";
$reviews = $connection->getConnection()->fetchAll($sql_reviews_all);

/*echo '<pre>';
print_r($reviews);
exit;*/

if(count($reviews) > 0){
    foreach ($reviews as $review) {
        $entity_pk_value = $review['entity_pk_value'];
        /*if($entity_pk_value != 4087)
        {
            continue;
        }*/
        $productIds[$entity_pk_value] = $entity_pk_value;
        $product_id = $entity_pk_value;
        $reviews_percentage = $review['per'];

        
        if(isset($entity_pk_value) && !empty($entity_pk_value))
        {

            foreach($starArrayValues as $rattingNum => $group)
            {
                if($reviews_percentage >= $group['min'] && $reviews_percentage <= $group['max'])
                {
                   // echo 'yes';
                    $finalRattingValue = $rattingNum;
                    break;
                }
            }
            //patch 9 April 2019 for zero not approved rating 
            if($reviews_percentage == 0)
            {
                $finalRattingValue = 'Not Rated';
            }
            
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
            
            $ratingTableName = $connection->getTableName('catalog_product_entity_int');
            $select_attribute = $connection->getConnection()->select()->from($ratingTableName)
                        ->where('attribute_id = ?',$attributeId)
                        ->where('store_id = ?',0)
                        ->where('entity_id = ?',$entity_pk_value);
            $attribute_found = $connection->getConnection()->fetchOne($select_attribute);
            if(isset($attribute_found) && !empty($attribute_found))
            {
                
                $detail_update = array();
                $detail_update['value'] = $finalRattingOptionValue;
                $condition_update = ["value_id = ?" => $attribute_found];
                $connection->getConnection()->update($ratingTableName, $detail_update, $condition_update);
               // echo "Review Updated Product ID = ".$entity_pk_value." </br>";
            }else
            {
                
                $detail_insert = array();
                $detail_insert['attribute_id'] = $attributeId;
                $detail_insert['store_id'] = 0;
                $detail_insert['entity_id'] = $entity_pk_value;
                $detail_insert['value'] = $finalRattingOptionValue;
                $connection->getConnection()->insert($ratingTableName, $detail_insert);
             //   echo "Review Inserted Product ID = ".$entity_pk_value." </br>";
            } 
            
        }
    }
}


//update non rated products
$products_tableName = $connection->getTableName('catalog_product_entity');
$sql_reviews_all = "SELECT entity_id FROM ". $products_tableName;
$products = $connection->getConnection()->fetchAll($sql_reviews_all);
$all_products_ids = array();
if(count($products) > 0){
    foreach ($products as $key => $value) {
        $all_products_ids[$value['entity_id']] = $value['entity_id'];
    }
}
$no_review_products = array_diff($all_products_ids, $productIds);
$finalRattingOptionValue = $attribute->getDefaultValue();
if(count($no_review_products) > 0){
    foreach ($no_review_products as $no_review_product) {
        $entity_pk_value = $no_review_product;

        $ratingTableName = $connection->getTableName('catalog_product_entity_int');
        $select_attribute = $connection->getConnection()->select()->from($ratingTableName)
                    ->where('attribute_id = ?',$attributeId)
                    ->where('store_id = ?',0)
                    ->where('entity_id = ?',$entity_pk_value);
        $attribute_found = $connection->getConnection()->fetchOne($select_attribute);
        if(isset($attribute_found) && !empty($attribute_found))
        {
            
            $detail_update = array();
            $detail_update['value'] = $finalRattingOptionValue;
            $condition_update = ["value_id = ?" => $attribute_found];
            $connection->getConnection()->update($ratingTableName, $detail_update, $condition_update);
           // echo "Review Updated Product ID = ".$entity_pk_value." </br>";
        }else
        {
            
            $detail_insert = array();
            $detail_insert['attribute_id'] = $attributeId;
            $detail_insert['store_id'] = 0;
            $detail_insert['entity_id'] = $entity_pk_value;
            $detail_insert['value'] = $finalRattingOptionValue;
            $connection->getConnection()->insert($ratingTableName, $detail_insert);
         //   echo "Review Inserted Product ID = ".$entity_pk_value." </br>";
        }
    }
}
echo 'Done';