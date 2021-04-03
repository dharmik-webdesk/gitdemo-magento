<?php

declare(strict_types=1);

namespace Ebizinfosys\CustomMetaData\Plugin\Block\Product;

use Magento\Catalog\Block\Product\View as Subject;
use Magento\Framework\View\Page\Config;

class View
{
    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * View constructor.
     * @param Config $pageConfig
     */
    public function __construct(Config $pageConfig)
    {
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param Subject $subject
     * @param $result
     * @return mixed
     */
    public function afterSetLayout(Subject $subject, $result)
    {
        $product = $subject->getProduct();
		$title 		= $product->getMetaTitle();

		if((empty($title) || trim($title) == trim($product->getName()))) 
    	{
    	    $currentCategoryName ='';
			$parentCategoryName ='';
			$productName = '';
			$categoryIds = $product->getCategoryIds();
			if(count($categoryIds))
				{
					$productCategory = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Catalog\Model\Category')->load($categoryIds[0]);
					$currentCategoryName =$productCategory->getData('alt_title')?$productCategory->getData('alt_title'):$productCategory->getName();
					if($productCategory->getParentCategory()->getName() && strtolower($productCategory->getParentCategory()->getName()) !=='all categories')
					{
							$parentCategoryName =$productCategory->getParentCategory()->getData('alt_title')?$productCategory->getParentCategory()->getData('alt_title'):$productCategory->getParentCategory()->getName();
					}
					$productName=str_replace('|', '', $product->getName());
					$productName=str_replace('"', '-inch', $productName);
					if($parentCategoryName && $currentCategoryName)
					{
						$newtitle=$productName.' | '.$currentCategoryName.' and '.$parentCategoryName.' for Sale - Compressor World Air Compressors, Dryers, & More';
						$newdesc='Buy '.$productName.' from our wide selection of '.$parentCategoryName.' for sale online! Browse our '.$currentCategoryName.' and other products to serve your business and industrial needs. Shop Compressor World today!';
					}
					else if($parentCategoryName =='' && $currentCategoryName)
					{
						$newtitle=$productName.' | '.$currentCategoryName.'  for Sale - Compressor World Air Compressors, Dryers, & More';
						$newdesc='Buy '.$productName.' from our wide selection of Compressors, Air Dryers for sale online! Browse our '.$currentCategoryName.' and other products to serve your business and industrial needs. Shop Compressor World today!';
				
					}	
			}
			else 
			{
				$productName=str_replace('|', '', $product->getName());
				$newtitle=$productName;
				$newdesc='Buy '.$productName.' from our wide selection of Compressors, Air Dryers, other related products for sale online! Browse our selection to find products to serve your business and industrial needs. Shop Compressor World today!';
		
			}
			$this->pageConfig->getTitle()->set($newtitle);
			$this->pageConfig->setDescription($newdesc);
		}

        return $result;
    }
}
