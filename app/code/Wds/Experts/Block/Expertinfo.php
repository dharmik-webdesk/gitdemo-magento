<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Wds\Experts\Block;

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
class Expertinfo extends \Magento\Framework\View\Element\Template
{
	/**
     * @var Registry
     */
    protected $registry;

    private $product;
    protected $_expertModel;
    protected $_masterHelper;

    public function __construct(
    	Registry $registry,
    	\Wds\Experts\Model\ResourceModel\Experts\Collection $_expertModel,
    	\Wds\Coreoverride\Helper\Master $_masterHelper
    )
    {
        $this->registry = $registry;
        $this->_expertModel = $_expertModel;
        $this->_masterHelper = $_masterHelper;
    }
	
	public function getExpertData()
    {	
			
	}
	
	public function getExpertInfo($expertid){
		$this->product = $this->registry->registry('product');
		$expertdata = "";
		if($expertid){	
			$attr = $this->product->getResource()->getAttribute('expert_id');
			if ($attr->usesSource()) {
				$expertlabel = $attr->getSource()->getOptionText($expertid);
			}
			//$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			//$expertmodel = $objectManager->get('Wds\Experts\Model\ResourceModel\Experts\Collection');
			$expert = $this->_expertModel->addFieldToFilter('name', $expertlabel);
			
			//$store = $objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore();
			$expertdata = $this->_expertModel->getData()[0];
			$imageurl = "";
			if($expertdata['photo']!=""){
				$imageurl = $this->_masterHelper->getStoreMediaUrl().$expertdata['photo'];
				
			}
			$expertdata['imageurl'] = $imageurl;
				
		}
		return $expertdata;
	}
		
}

