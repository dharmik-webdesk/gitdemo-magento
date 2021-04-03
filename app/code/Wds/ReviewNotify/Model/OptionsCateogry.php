<?php
namespace Wds\ReviewNotify\Model;
class OptionsCateogry  extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\Option\ArrayInterface
{
   
    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $CollectionFactory
    ) {
        parent::__construct($context);
        $this->_collectionFactory = $CollectionFactory;
    }

    public function getOptionArray()
    {
         $categoriesArray = $this->_collectionFactory->create()->addAttributeToSelect('name')
        ->addAttributeToSort('path', 'asc')
        ->addFieldToFilter('is_active', '1')
        ->load()
        ->toArray();
    
        $categories[-1]='Home Page';
        $categories[0]='All Category';
        foreach ($categoriesArray as $categoryId => $category) {
        if (isset($category['name'])) {
            $dash='';
            for($i=2;$i<=$category['level'];$i++)
                $dash.='-- ';
                if($categoryId!=2){
                $category['name']=$dash.$category['name'];
                $categories[$categoryId] = $category['name'];
                }
            }
        }
        return($categories);
    }

    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}