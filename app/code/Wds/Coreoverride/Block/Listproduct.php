<?php

namespace Wds\Coreoverride\Block;

use Zend_Db_Expr;
use \Magento\Catalog\Helper\Image;

class Listproduct extends \Magento\Framework\View\Element\Template
{
    
    
    protected $_coreRegistry;
    protected $_storeManager;
    protected $_datacache;
    protected $collectionData;
    protected $context;
    protected $imageHelper;
    protected $current_ads_collection;

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		\Magento\Review\Model\ReviewFactory $reviewFactory,
        Image $imageHelper
	)

    {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_datacache=array();
		$this->collectionData=null;
		$this->context=$context;
		$this->_productCollectionFactory = $productCollectionFactory;        
		$this->catalogProductVisibility = $catalogProductVisibility;
        $this->imageHelper = $imageHelper;
        $this->_reviewFactory=$reviewFactory;
        $current_ads_collection=array();

        parent::__construct($context);
    }

    
    public function getImageUrlByProduct($_product,$type,$width='204',$height='255'){
    	
		return $this->imageHelper->init($_product, $type, ['type'=>$type])->keepAspectRatio(true)->resize($width,$height)->getUrl();
    }

     public function getRatingSummary($product){ 
    	$this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
    	$ratingSummary = $product->getRatingSummary();
		return $ratingSummary;
	}

    public function getFeatureProductList(){
    	$product_list= $this->getData('product_ids');
		$product_list=trim($product_list);
		$product_list_array=explode(',',$product_list);
		if(count($product_list_array)>0){
			$store_id=$this->_storeManager->getStore()->getId();
			$product_collection=null;
			$product_collection = $this->_productCollectionFactory->create()
				->setStoreId($store_id)
				->addStoreFilter()
				->addFieldToFilter('entity_id', array('in' => $product_list_array))
				->addAttributeToSelect(array('name', 'price', 'small_image'))
				->addAttributeToSelect(array('special_price','call_for_price'))
				->addAttributeToSelect(array('cfm','warranty_info','product_tags'))
				->addAttributeToSelect('status')
				->setOrder('created_at', 'desc');
				
		    $product_collection->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $product_list_array).')'));
		}
		return $product_collection;
	}

}
