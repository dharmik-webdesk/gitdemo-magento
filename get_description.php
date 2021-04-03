<?php

ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';



$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');

$objectManager = \Magento\Catalog\Api\ProductRepositoryInterface::getInstance();

$sku = $_GET['sku'];

$_produc_id = $objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($sku);
$product = $objectManager->get('Magento\Catalog\Model\Product')->load($_produc_id);
$helper_factory = $objectManager->get('Magento\Catalog\Helper\Output'); ?>
<div class="description_mn" style="width:1000px;margin:0 auto;">
<?php 
 echo $helper_factory->productAttribute($product, $product->getDescription(), 'description');


 ?>
 </div>

<style>
.description_mn img{max-width:800px;}
</style>

<?php 

exit;




?>