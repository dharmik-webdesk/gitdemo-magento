<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Technicaldata\Block\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Technicaldata extends \Magento\Framework\View\Element\Template
{
    
    protected $_template = 'catalog/product/edit/technicaldata.phtml';
    protected $_resourceConnection;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\App\ResourceConnection $_resourceConnection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_resourceConnection = $_resourceConnection;
        parent::__construct($context, $data);
    }

   
    public function getTechnicalData(){

          //  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
          //  $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $this->_resourceConnection->getConnection();
            $tableName = $this->_resourceConnection->getTableName('technicaldata');

            $product = $this->getProduct();
            $product_id = $product->getId();
            $result = array();
            if(!empty($product_id)){
                $sql = "Select * FROM " . $tableName." where product_id =".$product_id;
                $result = $connection->fetchAll($sql);

                $sql = "Select attribute, attributegroup FROM " . $tableName . " GROUP by attribute, attributegroup";
                $result3 = $connection->fetchAll($sql);
                   
                $newresult=array();
                if(count($result3)>0){
                    foreach ($result3 as $key_first => $value_first) {  
                        $found=0;
                        foreach ($result as $key => $value) {
                            if($value['attribute']==$value_first['attribute'] && $value['attributegroup']==$value_first['attributegroup']){
                                $newresult[]=$value;
                                $found=1;
                                break;
                            }
                        }
                        if($found==0)
                            $newresult[]=array('attribute'=>$value_first['attribute'],'attributegroup'=>$value_first['attributegroup'],'attributevalue'=>'');

                    }
                }
                $result=$newresult;
            }else{
                $sql = "Select * FROM " . $tableName . " group by attribute, attributegroup";
                $result = $connection->fetchAll($sql);

                if(count($result)>0){
                    foreach ($result as $key => $value) {
                        $result[$key]['attributevalue'] = '';
                        $result[$key]['product_id'] = ''; 
                        //$result[$key]['attributegroup'] = ''; 
                    }
                }
            }

            if(empty($result) && count($result) == 0){
               $sql = "Select * FROM " . $tableName." GROUP by attribute ";
               $result = $connection->fetchAll($sql);
               if(count($result)>0){
                   foreach ($result as $key => $value) {
                       $result[$key]['attributevalue'] = '';
                       $result[$key]['product_id'] = ''; 
                       
                   }
               }
            }
            return $result;

    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Upload Files');
    }

    /**
     * Get tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Product Technical Data');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
	
	

}
