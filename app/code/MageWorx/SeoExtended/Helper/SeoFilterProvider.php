<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Helper;

use \MageWorx\SeoExtended\Helper\Data as HelperData;
use \MageWorx\SeoAll\Helper\Layer as HelperLayer;
use \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory as CategoryFilterCollectionFactory;

class SeoFilterProvider extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperLayer
     */
    protected $helperLayer;

    /**
     * @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\CollectionFactory
     */
    protected $categoryFilterCollectionFactory;

    /**
     * SeoFilterProvider constructor.
     * @param Data $helperData
     * @param HelperLayer $helperLayer
     * @param CategoryFilterCollectionFactory $categoryFilterCollectionFactory
     */
    public function __construct(
        HelperData $helperData,
        HelperLayer $helperLayer,
        CategoryFilterCollectionFactory $categoryFilterCollectionFactory
    ) {
        $this->helperData  = $helperData;
        $this->helperLayer = $helperLayer;
        $this->categoryFilterCollectionFactory = $categoryFilterCollectionFactory;
    }

    /**
     * @var MageWorx_SeoExtended_Model_Catalog_Category $seoFilter
     */
    protected $seoFilter;

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return bool|\Magento\Framework\DataObject|MageWorx_SeoExtended_Model_Catalog_Category
     */
    public function getSeoFilter($category, $storeId)
    {
        if ($this->seoFilter === null) {
            if (!$this->helperData->isUseSeoForCategoryFilters()) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }

            $currentFiltersData = $this->helperLayer->getLayeredNavigationFiltersData();
			
            $newFilterArray = array();
            
            
            if (empty($currentFiltersData)) {
                $this->seoFilter = false;
                return $this->seoFilter;
            }else{
                $optionids = $this->helperLayer->getCurrentLayeredFilters();
				if(count($optionids)){
					$filteredOptionIdArray = array();
						
					foreach($optionids as $optionid){
						$filterdOptions = $optionid->getValue();
						// skip price filterd
						if(!is_array($filterdOptions))
							$filteredOptionIdArray[] =  $filterdOptions;
					}//exit;
					asort($filteredOptionIdArray);
						//echo '<pre>';
					//print_r($filteredOptionIdArray);
					//exit;
					$newFilterArray = array(); 
					$i = 0;
					/*foreach($currentFiltersData as $currentData){
						$newFilterArray[$filteredOptionIdArray[$i]] = $currentData;
						$i++;
					}*/
				}	
            }
			
            if (count($currentFiltersData) > 1 && $this->helperData->isUseOnSingleFilterOnly()) {
             
                $this->seoFilter = false;
                return $this->seoFilter;
            }
           
            $attributeIds = array_keys($currentFiltersData);
            

            /** @var \MageWorx\SeoExtended\Model\ResourceModel\CategoryFilter\Collection $collection */
            $collection = $this->categoryFilterCollectionFactory->create();
			$collection->getFilteredCollection($category->getId(), $storeId);
			
			
			$collection_option_id = array();
			$str_filteredOptionIds = implode(",",$filteredOptionIdArray);
			
			foreach($collection as $key=>$value){
				$attributeIds = $value->toArray();
				$option_id = $attributeIds['attribute_option_id'];
				$option_ids1 = explode(',',$option_id);
				asort($option_ids1);
				$str_option_ids1 = implode(",",$option_ids1);
				if($str_filteredOptionIds == $str_option_ids1){
					//echo $str_filteredOptionIds.'=='.$str_option_ids1.'<br/>';
					 $this->seoFilter = $this->getFilterBySortOrder($currentFiltersData, $collection, $str_filteredOptionIds);
					
				}
					
			}
			
           
          // $this->seoFilter = $currentFiltersData;
        }
		//exit;
        return $this->seoFilter;
    }

    /**
     * @param array $currentFiltersData
     * @return \MageWorx\SeoExtended\Model\CategoryFilter|false
     */
    protected function getFilterBySortOrder($currentFiltersData, $collection, $str_filteredOptionIds)
    {
        $hightPriorityFilter = false;
        $currentPosition    = false;

        /*foreach ($collection->getItems() as $filter) {

            if (!$hightPriorityFilter || (int)$currentFiltersData[$filter->getAttributeId()]['position'] < $currentPosition) {
                $hightPriorityFilter = $filter;
                //$currentPosition = (int)$currentFiltersData[$filter->getAttributeId()]['position'];
            }
        }*/
		foreach ($collection->getItems() as $filter) {
				
				$attributeIds = $filter->toArray();
				$option_id = $attributeIds['attribute_option_id'];
				$option_ids1 = explode(',',$option_id);
				asort($option_ids1);
				$str_option_ids1 = implode(",",$option_ids1);
            if (!$hightPriorityFilter && $str_filteredOptionIds==$str_option_ids1) {
				
                $hightPriorityFilter = $filter;
				$currentPosition = 5;
              
            }
        }
        return $hightPriorityFilter;
    }
}
