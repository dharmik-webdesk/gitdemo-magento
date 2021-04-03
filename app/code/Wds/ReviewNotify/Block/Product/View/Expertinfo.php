<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile
namespace Wds\ReviewNotify\Block\Product\View;

/**
 * Detailed Product Reviews
 *
 * @api
 * @since 100.0.2
 */

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

/**
 * Catalog navigation
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Expertinfo  extends \Magento\Framework\View\Element\Template
{
	/**
     * @var Registry
     */
	public $expertcollection;
	public $_storeManager;
	public $_cache_expert= [];

	public function __construct(\Magento\Framework\View\Element\Template\Context $context,
								\Magento\Store\Model\StoreManagerInterface $storeManager,
   								\Wds\Experts\Model\ResourceModel\Experts\Collection  $expertcollectio
   								 )
    {
    	parent::__construct($context);	
    	$this->expertcollection=$expertcollectio;
    	$this->_storeManager = $storeManager;
    }

    public function getExpertInfo(){
    	$expertdata=array();
    	$expoert_id=(string)$this->getData('experts_id');
		if($expoert_id){
    		if(isset($this->_cache_expert[$expoert_id])){
    			$expertdata= $this->_cache_expert[$expoert_id];
    		}else{

    			$info=$this->expertcollection->addFieldToFilter('id', $expoert_id);
    			if(count($info)>0){
    				$expertdata = $info->getData()[0];
    				$imageurl = "";
					if($expertdata['photo']!=""){
						$imageurl = $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ) . $expertdata['photo'];
					}
					$expertdata['imageurl'] = $imageurl;
				}

				$this->_cache_expert[$expoert_id]=$expertdata;
				if(count($info))
					$info->clear()->getSelect()->reset('where');;
			}	
    	}
    	return $expertdata;
	}
}
