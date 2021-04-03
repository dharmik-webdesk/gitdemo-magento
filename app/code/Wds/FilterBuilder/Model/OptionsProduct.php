<?php
namespace Wds\FilterBuilder\Model;
class OptionsProduct  extends \Magento\Framework\View\Element\Template
{
   
    /*public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }*/

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product $CollectionProduct
    ) {
        parent::__construct($context);
        $this->_collectionProduct = $CollectionProduct;
    }

    public function getProductName($id)
    {
            if($id){
                $productObject =  $this->_collectionProduct;
                $product      = $productObject->load($id);
                return $product->getName();
            }else{
                return '';
            }
    }

    
}