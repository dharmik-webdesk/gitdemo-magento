<?php
namespace Wds\ReviewNotify\Model;

class OptionsExperts  extends \Magento\Framework\View\Element\Template implements
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
        \Wds\Experts\Model\ExpertsFactory $CollectionFactory
    ) {
        parent::__construct($context);
        $this->_collectionFactory = $CollectionFactory;
    }

    public function getOptionArray()
    {
        $categoriesArray = $this->_collectionFactory->create()->getCollection();
        $categoriesArray = $categoriesArray->toArray();
        $categories=array();
        $categories[0]='Select Experts';
        if($categoriesArray){
            foreach ($categoriesArray['items'] as $categoryId => $category) {
               $categories[$category['id']] = $category['name'];
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