<?php
namespace Wds\FilterBuilder\Model;

class OptionsCateogry  extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\Option\ArrayInterface
{
   
    public $_attibute_helper;

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
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection,
        \Wds\FilterBuilder\Helper\Data $attibute_helper
        
    ) {
        parent::__construct($context);
        $this->_entityAttribute = $entityAttribute;
        $this->_attributeOptionCollection = $attributeOptionCollection;
        $this->_attibute_helper=$attibute_helper;
    }

    public function getOptionArray(){
        $attributeId=$this->_attibute_helper->getAttributeId();
        $optionDetailArray= $this->_attributeOptionCollection
                ->setOrder('default_value','asc')
                ->setAttributeFilter($attributeId)
                ->setStoreFilter()
                ->load();
        $optAray=array();
        $optAray['']='Select Configuration';
        foreach ($optionDetailArray as $key => $opt) {
               $OptinoId=$opt['option_id'];
            $optAray[$OptinoId] = $opt['default_value'];
        }
        return($optAray);
    }

    public function getAllOptions(){
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            if($index==0)
                $index='';

            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function getOptionText($optionId){
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}