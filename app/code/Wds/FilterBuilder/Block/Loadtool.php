<?php

namespace Wds\FilterBuilder\Block;

use Zend_Db_Expr;
use \Magento\Catalog\Helper\Image;

class Loadtool extends \Magento\Framework\View\Element\Template{
        
    protected $_categoryFactory;
    protected $_storeManager;
    protected $context;    

    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Framework\View\Asset\Repository $Repository 
	    
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $context->getStoreManager();
	$this->Repository = $Repository;
        parent::__construct($context);
    }

   function getAssetUrl($asset) {
        return $this->Repository->createAsset($asset)->getUrl();
    }

    public function getFilterList(){
            $this->filter_list=array(
                                    'brand'=>'Brand',
                                  //  'configuration'=>'Configurations',
                                    'power'=>'Power',
                                    'rpm'=>'Voltage',
                                    'horsepower'=>'Horsepower',
                                    'cfm'=>'Capacity',
                                    'compability_size'=>'Compatibility Size',
                                    'view_product'=>'View Product',
                                );
	return             $this->filter_list;
    }

    public function getCategory()
    {
        $category_data=array();
        $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $category_ids=$this->getData('category_ids');
        $category_ids=explode(',',$category_ids);
        $categories = $this->_categoryFactory->create();
        $categories->addAttributeToSelect('*');   
        $categories->addAttributeToFilter('level' , 2);   
        $categories->addAttributeToFilter('is_active' , 1)
                   ->addFieldToFilter('entity_id', array('in' => $category_ids));

        $categories->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $category_ids).')'));
        
        if(count($category_ids)>0){

        $index=0;
        foreach($categories as $category){
            $category_data[$index]['name'] = $category->getName();
            $thumb_url = $category->getData('image_thumb');
            if($thumb_url!="")
                $category_data[$index]['thumb'] = $base_url.'catalog/category/'.$thumb_url;
            else
                $category_data[$index]['thumb'] = $base_url.'catalog/category/noimage.jpg';

            $category_data[$index]['id'] = $category->getId();
            $category_data[$index]['url'] = $category->getURL();
            $index++;
        }  }
       return $category_data;
    }
}
