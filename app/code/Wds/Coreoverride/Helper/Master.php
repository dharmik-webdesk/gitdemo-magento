<?php

namespace Wds\Coreoverride\Helper;

class Master extends \Magento\Framework\App\Helper\AbstractHelper
{
		
		protected $_storeManager;
		protected $_brandCollection;
		protected $_eavConfig;
		protected $_productCollection;
		protected $_resourceConnection;
		protected $_categoryFactory;
		protected $_registry;
		protected $_requestInterface;
		protected $_session;
	
		public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Store\Model\StoreManagerInterface $_storeManager,
			\Mageplaza\Shopbybrand\Model\ResourceModel\Brand\Collection $_brandCollection,
			\Magento\Eav\Model\Config $_eavConfig,
			\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_productCollection,
			\Magento\Catalog\Model\CategoryFactory $_categoryFactory,
			\Magento\Framework\App\ResourceConnection $_resourceConnection,
			\Magento\Framework\Registry $_registry,
			\Magento\Framework\App\Http\Context $httpContext,
			\Magento\Framework\App\RequestInterface $_requestInterface,

			\Magento\Customer\Model\Session $_session
		){
				$this->_storeManager = $_storeManager;
				$this->_brandCollection = $_brandCollection;
				$this->_eavConfig = $_eavConfig;
				$this->_productCollection = $_productCollection;
				$this->_resourceConnection = $_resourceConnection;
				$this->_categoryFactory = $_categoryFactory;
				$this->_registry = $_registry;
				$this->_requestInterface = $_requestInterface;
				$this->_session = $_session;
				$this->httpContext = $httpContext;
				parent::__construct($context);
		}
		
		function getStoreBaseUrl(){
			return $this->_storeManager->getStore()->getBaseUrl();
		}

		function getStoreMediaUrl(){
			return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		}

		function getCurrentStore(){
			return $this->_storeManager->getStore();
		}

		function getRequestedRouteName(){
			return $this->_requestInterface->getRouteName();
		}
		function getRequestedControllerName(){
			return $this->_requestInterface->getControllerName();
		}

		function getStoreCurrentCategory(){
        	return $this->_registry->registry('current_category');
    	}

    	function getStoreCurrentProduct(){
        	return $this->_registry->registry('current_product');
    	}
    	function getStoreResourceConnection(){
			return $this->_resourceConnection->getConnection();
		}

    	function getStoreCurrentBrand(){
        	return $this->_registry->registry('current_brand');
    	}
		function getLoggedInCustomerName(){
			$customerName = "";
			$isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
			if($isLoggedIn) {
				 $customerName = $this->_session->getCustomer()->getName();
			}
			//echo "NAme: ".$customerName;
			return $customerName;
		}

		// Show brand Image/logo with Product Listing
		function getMageplazaBrandList(){

			$_brand_data = array();
			$magebrandCollection = $this->_brandCollection->load();
			if($magebrandCollection){
				foreach($magebrandCollection as $brand){
	            	$_brand_data[$brand['option_id']]=$brand['image'];
	        	}
	        }
	        return $_brand_data;
		}
		
		// Product Tag show with Product Listing Like Price Match Guarantee
		function getProductTagList(){

			$attribute = $this->_eavConfig->getAttribute('catalog_product', 'product_tags');
			$allOptions = $attribute->getSource()->getAllOptions();
	        return $allOptions;
		}

		function getNextProductUrl($_product_id){

				$collection = $this->_productCollection->create()
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('url_key')
				->addAttributeToFilter('entity_id', array('gt' => $_product_id))
				->addAttributeToFilter('status', 1)
				->addAttributeToSort('entity_id','asc')
				->setPageSize(1)
				->setCurPage(1)
				->load()->toArray();

			foreach($collection as $product){
				if($product['entity_id'] > 0){
					return $this->getStoreBaseUrl().$product['url_key'].'.html';
				}
			}
			return false;
		}

		function getPreviousProductUrl($_product_id){

			$collection = $this->_productCollection->create()
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('url_key')
				->addAttributeToFilter('entity_id', array('lt' => $_product_id))
				->addAttributeToFilter('status', 1)
				->addAttributeToSort('entity_id','desc')
				->setPageSize(1)
				->setCurPage(1)
				->load()->toArray();
			
			foreach($collection as $product){
				if($product['entity_id'] > 0){
					return $this->getStoreBaseUrl().$product['url_key'].'.html';
				}
			}
			return false;
		}

		function getTechnicalData($_product_id, $key){

			$connection = $this->_resourceConnection->getConnection();
			$tableName = $this->_resourceConnection->getTableName('technicaldata');
			$attributesql = "Select * FROM " . $tableName." where product_id =".$_product_id. " AND attributegroup='".$key."' order by attribute ASC";
    		$resultcol = $connection->fetchAll($attributesql);

    		return $resultcol;
		}

		function getSubcategorySlider($parent_category_id){

            $category_data = array();
            $category = $this->_categoryFactory->create()->load($parent_category_id);
            $category_data['name'] = $category->getName();

            $subcats = $category->getChildrenCategories();
            $index = 0; 
            if(count($subcats)>0){ 
                foreach ($subcats as $_subcategory){
                	if($_subcategory->getIsActive()){
                		$_subcategory = $this->_categoryFactory->create()->load($_subcategory->getId()); 
	                    $category_data['child'][$index]['name'] = $_subcategory->getName();
	                    $thumb_url = $_subcategory->getData('image_thumb');
	                    if($thumb_url!="")
	                         $category_data['child'][$index]['thumb'] = $this->getStoreMediaUrl().'catalog/category/'.$thumb_url;
	                    else
	                        $category_data['child'][$index]['thumb'] = $this->getStoreMediaUrl().'catalog/category/noimage.jpg';
	                    $category_data['child'][$index]['url'] = $_subcategory->getURL();
	                    $index++;
	                }
                }
            }
            return $category_data;
    	}

    	function getStoreMobileMenu($parent_category_id){

            $category_data = array();
            $category = $this->_categoryFactory->create()->load($parent_category_id);
            
			$subcats = $category->getChildrenCategories();
            $index = 0; 
            if(count($subcats)>0){ 
                foreach ($subcats as $_subcategory){
                	if($_subcategory->getIsActive()){
                		$category_data[$index]['category_id'] = $_subcategory->getId();
	                    $category_data[$index]['name'] = $_subcategory->getName();
	                    $category_data[$index]['url'] = $_subcategory->getURL();
	                    $index++;
	                }
                }
            }
            return $category_data;
    	}



		
}
?>
