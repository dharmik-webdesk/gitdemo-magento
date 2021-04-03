<?php


ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');

$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

        $layerResolver = $objectManager->get(\Magento\Catalog\Model\Layer\Resolver::class);
        
        $layerResolvers = $layerResolver->create('search');
        $layerResolvers1 = $layerResolver->get();
        $productCollection = $layerResolvers1->getProductCollection();

        echo "Search layered navigation :<br>";
        echo "products count == ".count($productCollection)."<br>";

        exit;

/*$productManager = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');


                           //addCategoryFilter($category)
                           $product_collection = $productManager->addAttributeToSelect(array('name', 'price', 'small_image'))
                           ->addAttributeToSelect(array('special_price','show_in_menu2'))
                           ->addAttributeToSelect('status')
                           ->addAttributeToFilter('show_in_menu2', 1)
                           ->setPageSize(4)
                           ->setOrder('entity_id','desc');

                           echo $product_collection->count(); exit;

 */

            $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                    ->addAttributeToSelect(array('name','image','price','special_price','wwe_free_shipping','free_freight'));
        
        

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load(4061);
        $_imageHelper = $objectManager->get('Magento\Catalog\Helper\Image');
        echo $_imageHelper->init($product, 'small_image', ['type'=>'small_image'])->getUrl();

       exit;               

?>