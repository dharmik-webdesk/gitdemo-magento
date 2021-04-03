<?php
/**
 * @copyright Copyright (c) 2016 https://chillydraji.wordpress.com
 */
namespace Wds\Productsgrid\Model\Category;

class Categorylist implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->_categoryCollectionFactory = $collectionFactory;
        
    }

    public function toOptionArray($addEmpty = true)
    {
		
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        

        $collection = $this->_categoryCollectionFactory->create();

        $collection->addAttributeToSelect('name')
                ->addAttributeToSort('path', 'asc')
                ->addFieldToFilter('is_active', '1')
                ->load();
                

        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Category --'), 'value' => ''];
        }
  
        foreach ($collection as $category) {
                if ($category->getName()) {
                    $dash='';
                    for($i=2;$i<=$category->getLevel();$i++)
                        $dash.='-- ';
                        $options[] = ['label' => $dash.$category->getName(), 'value' => $category->getId()];
                }
        }


        return $options;
    }
}
