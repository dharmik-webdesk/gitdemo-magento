<?php

namespace Wds\Coreoverride\Block;

class Listcategory extends \Magento\Framework\View\Element\Template
{
    
    
    protected $_categoryFactory;
    protected $_storeManager;
    protected $context;    

    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
	    \Magento\Catalog\Model\CategoryFactory $categoryFactory
	    
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context);
    }

    public function getCategory()
    {
        $base_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $category_ids = $this->getData('category_ids');
        $category_ids = trim($category_ids);
        $category_ids_array = explode(',',$category_ids);
        $category_data = array();
        $index = 0;
        if(count($category_ids_array)>0){
            foreach ($category_ids_array as $cat_id) {
               $category = $this->_categoryFactory->create()->load($cat_id); 
               $category_data[$index]['name'] = $category->getName();
               $thumb_url = $category->getData('image_thumb');
               if($thumb_url!="")
                    $category_data[$index]['thumb'] = $base_url.'catalog/category/'.$thumb_url;
                else
                    $category_data[$index]['thumb'] = $base_url.'catalog/category/noimage.jpg';
               $category_data[$index]['url'] = $category->getURL();
               $index++;
            }
        }
        return $category_data;
    }
}