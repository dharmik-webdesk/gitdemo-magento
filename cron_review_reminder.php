<?php 
ini_set("memory_limit","50000M"); 
set_time_limit(0);
//ini_set('error_reporting',1);
use Magento\Framework\App\Bootstrap;
$path= __DIR__;
require $path.'/app/bootstrap.php';

echo 'Current Date'. date('Y-m-d H:i:s');
echo '<br>';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$url = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
$mediaurl= $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('frontend');

$review_helper=$objectManager->create('\Wds\ReviewNotify\Helper\Data');
$from_date=date('Y-m-d 00:00:00', strtotime('-2 week'));
$to_date=date('Y-m-d 23:59:59', strtotime('-2 week'));
$review_collection = $objectManager->create('Wds\ReviewNotify\Model\Customreview');
$review_collection_data= $review_collection->getCollection();
$review_collection_data = $review_collection_data
                              ->addFieldToFilter('dt', array('from'=>$from_date, 'to'=>$to_date))
                              ->addFieldToFilter('resend',0)
                              ->addFieldToFilter('status',0);
$review_collection_data->getSelect();
$r_total_notification=0;
if($review_collection_data->getSize()){
    foreach($review_collection_data as $_review){ 
        $data=$_review->getData();
        $data['tracking']=$_review->getId();    
        //$data['customer_email']='testing@compressorworld.com';

        $review_helper->sendEmail($data);
        $review_collection->load($_review->getId());
        $data['resend']=1;
        $data['resend_date']=date('Y-m-d H:i:s');

        $review_collection->setData($data);
        $review_collection->save();
        $r_total_notification++;
    }
}
echo $r_total_notification. " resent notification send to the customer <bR />";

$order = $objectManager->create('Magento\Sales\Model\Order')->getCollection();
$from_date=date('Y-m-d 00:00:00', strtotime('-15 days'));
$to_date=date('Y-m-d 23:59:59', strtotime('-15 days'));
$order = $order->addAttributeToFilter('created_at', array('from'=>$from_date, 'to'=>$to_date));
//$order = $order->addFieldToFilter('entity_id', '1100');
$orderData=array();

$resources = $url->get('Magento\Framework\App\ResourceConnection');
$_productManager = $url->get('\Magento\Catalog\Model\Product');
$imageHelper  = $objectManager->get('\Magento\Catalog\Helper\Image');
$connection= $resources->getConnection();
$tableName = $connection->getTableName('wds_offline_review_reminder');
$total_notification=0;

if($order->getSize()){

    $storeid= $storeManager->getStore()->getId();
    $scopeConfig = $url->get('Magento\Framework\App\Config\ScopeConfigInterface');
    $transportBuilder = $url->get('Magento\Framework\Mail\Template\TransportBuilder');
    $inlineTranslation = $url->get('Magento\Framework\Translate\Inline\StateInterface');
  
    $sender['email'] = $scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    $sender['name'] = $scopeConfig->getValue('trans_email/ident_general/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeid);

    
    foreach($order as $_order){ 
        // Checking if the order place as a guest or not
        $c_email=$_order->getData('customer_email');
        $c_id=$_order->getCustomerId();
        if($c_id){
            $c_first_name=$_order->getData('customer_firstname');
            $c_last_name=$_order->getData('customer_lastname');
        }else{
           $billing= $_order->getBillingAddress()->getData();
           $c_first_name=$billing['lastname'];
           $c_last_name=$billing['firstname'];
           if(empty($c_email)){
            $c_email=$billing['email'];
           }
        }

        $orderData[$_order->getId()]['date'] = $_order->getData('created_at');
        $orderItems = $_order->getAllItems();
        foreach($orderItems as $item)
        {
            $select = $connection->select()->from($tableName)->where('order_id = :order_id AND product_id = :product_id');
            $detailId = $connection->fetchOne($select, [':order_id' => $_order->getId(),':product_id' => $item->getId()]);
            if (empty($detailId)) {
              $total_notification++;
              $product = $_productManager->load($item->getProductId());
              $prodname = $product->getName();
              $produrl = $product->getProductUrl();
              $prodsku= $product->getSku();
              $image_url=$imageHelper->init($product, 'small_image', ['type'=>'small_image'])->keepAspectRatio(true)->resize('204','255')->getUrl();
              $product_image=$image_url;
              $produrl = $produrl.'#write_review';

              $templateVars = array(
                      'name' => $c_first_name.' '.$c_last_name,
                      'product_name' => $prodname,
                      'product_sku' => $prodsku,
                      'product_image' => $product_image,
                      'write_review_link' => $produrl
                    );
             
             
              $to = $c_email;
             // $to = array('testing@compressorworld.com');
              $inlineTranslation->suspend();
              $transport = $transportBuilder->setTemplateIdentifier(9)
                      ->setTemplateOptions($templateOptions)
                      ->setTemplateVars($templateVars)
                      ->setFrom($sender)
                      ->addTo($to)
                      ->getTransport();
               $transport->sendMessage();
               $inlineTranslation->resume();
                
                $detail['order_id'] = $_order->getId();
                $detail['customer_name'] = $c_first_name.' '.$c_last_name;
                $detail['customer_email'] = $c_email;
                $detail['customer_magento_id'] =$c_id;
                $detail['product_id'] =$item->getId();
                $detail['status'] =0;
                $connection->insert($tableName, $detail);
                
            } 
        }
    }
}
echo $total_notification. " Notifications sent";