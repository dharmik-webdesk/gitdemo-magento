<?php

namespace Wds\ReviewNotify\Block;
use Zend_Db_Expr;
use \Magento\Catalog\Helper\Image;

class ListRating extends \Magento\Framework\View\Element\Template
{
    
    
    protected $_coreRegistry;
    protected $_reviewFactory;
    protected $_storeManager;
    protected $_datacache;
    protected $collectionData;
    protected $context;
    protected $imageHelper;
    protected $current_ads_collection;
    protected $_ratingFactory;
    protected $_voated_result;
    protected $_ratingLabelFactory;
    protected $_productloader;
    protected $_product_cache;
    
    
    

	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $ratingFactory,
        \Magento\Review\Model\RatingFactory $ratingLabelFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Catalog\Model\ProductFactory $productloader
      
	)

    {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_ratingFactory=$ratingFactory;
        $this->_ratingLabelFactory = $ratingLabelFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->_productloader = $productloader;
        $this->context=$context;
		parent::__construct($context);
    }

    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }

    public function getProductDetail()
    {
		if(!$this->_product_cache){
			$product = $this->_productloader->create()->load($this->getProductId());	
			$this->_product_cache=$product;
		}
		return $this->_product_cache;
    }


    public function getRatingSummary(){ 
		$product=$this->getProductDetail();
    	$this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
    	$ratingSummary = $product->getRatingSummary()->getRatingSummary();
    	return $ratingSummary;
	}

	public function getReviewsCount(){ 
		$product=$this->getProductDetail();
    	$_reviewCount = $product->getRatingSummary()->getReviewsCount();
    	return $_reviewCount;
	}

	

    public function getRatings(){
        return $this->_ratingLabelFactory->create()->getResourceCollection()->addEntityFilter(
            'product'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems();
    }

    public function getVotesd_result(){
		return $this->_voated_result;
    }
    public function advanceReview(){
		$currentStoreId = $this->_storeManager->getStore()->getId();

		$_reviewsCollection = $this->_ratingFactory->create()->addStoreFilter(
            $currentStoreId
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProductId()
        )->addRateVotes()->setDateOrder();

		$_reviewsCollection->getSelect();
		$this->_voated_result=array();
		$_totalReview=0;
		$total_review=0;

		$count_wise_start=array();
		for($i=5;$i>=1;$i--){
   			$count_wise_start[$i]=0; 
		}

		$Final_rating=0;
		foreach ($_reviewsCollection->getItems() as $_review){   $_totalReview++;
    		$_votes = $_review->getRatingVotes(); 
    		if ($total_review=count($_votes)){
        		$one_user_rating=0;
        		foreach ($_votes as $_vote){
            		if(isset($this->_voated_result[$_vote->getRatingCode()])){
               			$this->_voated_result[$_vote->getRatingCode()]['rating']=$this->_voated_result[$_vote->getRatingCode()]['rating']+$_vote->getPercent();
               			$this->_voated_result[$_vote->getRatingCode()]['count']=$this->_voated_result[$_vote->getRatingCode()]['count']+1;
               			$this->_voated_result[$_vote->getRatingCode()]['value']=$this->_voated_result[$_vote->getRatingCode()]['value']+$_vote->getValue();
            		}else{
                 		$this->_voated_result[$_vote->getRatingCode()]['rating']=$_vote->getPercent();
                 		$this->_voated_result[$_vote->getRatingCode()]['count']=1;
                 		$this->_voated_result[$_vote->getRatingCode()]['value']=$_vote->getValue();
            		}
            		$one_user_rating=$one_user_rating+$_vote->getValue();
        		}
        		$one_user_rating=ceil($one_user_rating/count($_votes));
        		$count_wise_start[$one_user_rating]=$count_wise_start[$one_user_rating]+1;
    		}
		}
		if(count($this->_voated_result)==0){
			$rating_collection= $this->getRatings();
			foreach ($rating_collection as $key => $value) {
				$this->_voated_result[$value->getRatingCode()]['rating']=0;
				$this->_voated_result[$value->getRatingCode()]['count']=0;
				$this->_voated_result[$value->getRatingCode()]['value']=0;
			}
		}
		return $count_wise_start;
	}

}
