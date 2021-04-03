<?php
namespace Wds\CategoryThumb\Model\Category;
 
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
	protected function addUseDefaultSettings($category, $categoryData)
	{
    	$data = parent::addUseDefaultSettings($category, $categoryData);
 
    	if (isset($data['image_thumb'])) {
            unset($data['image_thumb']);
 
            $data['image_thumb'][0]['name'] = $category->getData('image_thumb');
            $data['image_thumb'][0]['url']  	= $category->getImageUrl('image_thumb');
    	}
 
    	return $data;
	}
 
	protected function getFieldsMap()
	{
    	$fields = parent::getFieldsMap();
        $fields['content'][] = 'image_thumb'; // NEW FIELD
    	
    	return $fields;
	}
}