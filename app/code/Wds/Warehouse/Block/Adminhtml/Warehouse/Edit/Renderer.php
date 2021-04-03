<?php
namespace Wds\Warehouse\Block\Adminhtml\Warehouse\Edit;


class Renderer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
    * Get the after element html.
    *
    * @return mixed
    */
    public function getAfterElementHtml()
    {
			
        // here you can write your code.
        $customDiv = "<img src='http://localhost/mage2demo/pub/media/warehouse/h/g/hgen_air_dryers_1_1.jpg' width='200' />";
        return $customDiv;
    }
}
