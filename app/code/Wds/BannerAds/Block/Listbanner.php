<?php

namespace Wds\BannerAds\Block;

class Listbanner extends \Magento\Framework\View\Element\Template
{
    protected $_reviewsFactory;
    protected $_reviewsCollection;
    protected $_coreRegistry;
    protected $_banneradFactory;
    protected $_storeManager;
    protected $_datacache;
    protected $collectionData;
    protected $context;	
	

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wds\BannerAds\Model\BanneradFactory $BanneradFactory

    )
    {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_banneradFactory = $BanneradFactory;
        $this->_datacache=array();
	$this->collectionData=null;
		$this->context=$context;
        parent::__construct($context);
    }

    public function getImageUrl($name){
	$media_url=$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	if($name){
		return $media_url.$name;
	}
	return false;
    }

    public function getBannerHtml($image_attr='',$url_attr=''){
	$html='';
	$mainImage=$this->collectionData->getData($image_attr);
	if($mainImage){
	  $mainImage=$this->getImageUrl($mainImage);
	  if($imageurl=$this->collectionData->getData($url_attr)){
	     $html= '<a href="'.$imageurl.'"><img src="'.$mainImage.'" alt="Banner ads" /></a>';	 	
	  }else{
	     $html= '<img src="'.$mainImage.'" alt="Banner ads" />';	
	  }	
	}
	return $html;
    }	
    
    public function getBanners($pos='all'){
	$collection = $this->_banneradFactory->create()->getCollection();
	$collection->addFieldToSelect('*');
		
	$store_id=$this->_storeManager->getStore()->getId();
	$collectionData=null;
	
	$router=$this->context->getRequest()->getRouteName();
	if($router=='onestepcheckout'){
		if(isset($this->_datacache['checkout'])){
			$collectionData=$this->_datacache['checkout'];
		}else{
			$collection->addFieldToFilter('store_id',array(array('finset'=>$store_id),array('finset'=>0)));
			$collection->addFieldToFilter('status',0);
			$collection->addFieldToFilter('show_on_checkout',1);
			$collection->setOrder('id','desc');
			$collectionData=$collection->getFirstItem();
			$this->_datacache['checkout']=$collectionData;
		}	
	}else{
		$category = $this->_coreRegistry->registry('current_category');
		if($category){
		if(isset($this->_datacache[$category->getId()])){
			$collectionData=$this->_datacache[$category->getId()];
		}else{
			$collection->addFieldToFilter('store_id',array(array('finset'=>$store_id),array('finset'=>0)));
			$collection->addFieldToFilter('category_id',$category->getId());
			$collection->addFieldToFilter('status',0);
			$collection->setOrder('store_id','desc');
			if($collection->getSize()>0)
			   $collectionData=$collection->getFirstItem();
			else{
			   $collection = $this->_banneradFactory->create()->getCollection();
			   $collection->addFieldToFilter('store_id',array(array('finset'=>$store_id),array('finset'=>0)));
			   $collection->addFieldToFilter('category_id',0);
				$collection->addFieldToFilter('status',0);
			   $collectionData=$collection->getFirstItem();
			}
			$this->_datacache[$category->getId()]=$collectionData;
		}
		}
	}
        $this->collectionData=$collectionData;
	return $collectionData;
    }



}
