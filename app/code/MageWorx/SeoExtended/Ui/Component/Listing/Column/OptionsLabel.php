<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoExtended\Ui\Component\Listing\Column;

class OptionsLabel extends \Magento\Ui\Component\Listing\Columns\Column
{
  
    protected $urlBuilder;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
    
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
           foreach ($dataSource['data']['items'] as & $item) {

                $option_label = 0;
                if(!empty($item['attribute_option_id']) && !is_null($item['attribute_option_id'])){
                    $attributeOptionId = explode(",", $item['attribute_option_id']);
                    $optionlabel = $this->getOptionLabel($attributeOptionId);
                }
                $item[$this->getData('name')] = $optionlabel;//Value which you want to display
               
            }     
        }
        return $dataSource;
    }

     public function getOptionLabel($attributeOptionId){
        
        $option_label = "";
        //print_r($attributeOptionId);
        if(count($attributeOptionId)){
            //KM customize for option label
            $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
                $attributeSetCollection = $objectManager->create('Magento\Eav\Model\Config')
                                            ->getEntityType('catalog_product')
                                            ->getAttributeCollection()
                                            ->addSetInfo();
             $option_label1=array();  
            foreach ($attributeSetCollection as $id=>$attributeSet) {
                
                if ($attributeSet->usesSource() && $attributeSet->getIsFilterable()){
                    if($attributeSet->getFrontendLabel()){
                        
                        foreach ($attributeSet->getSource()->getAllOptions() as $option)
                        {
                            if(in_array($option['value'], $attributeOptionId)){
                                if($option['value'])
                                  
                                $option_label1[]=$option['label'];
                            } 
                        }
                    }
                }   
            }
            $option_labelarray = array();
            if(count($option_label1)){
                $option_label = implode(', ',$option_label1);
            }
        } 
        return $option_label;
    }
}
