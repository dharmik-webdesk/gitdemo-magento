<?php
namespace Wds\Technicaldata\Observer;

class Eventobserver implements \Magento\Framework\Event\ObserverInterface
{
	 private $ResourceConnection;
	public function __construct(
    \Magento\Framework\App\RequestInterface $request,
    \Magento\Framework\App\ResourceConnection $ResourceConnection 
	)
	{
	    $this->_request = $request;
	    $this->resourceConnection = $ResourceConnection;
	}
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$params = $this->_request->getParams();
		$product= $observer->getData('product');
		
        if ($product) {
            $productId = $product->getId();
            
            

            if(isset($params['tech_input_data_name']))
            {
            	
            	$customDescDataAtt = $params['tech_input_data_name'];
	            $customDescDataAttVal = $params['tech_input_data_value'];
	            $customDescDataAttGroup = $params['tech_input_group_name'];
	            $numberofelements = count($customDescDataAtt);

	            
	            $attributeData = array();
	            foreach ($customDescDataAtt as $key => $value) {
	               if(isset($customDescDataAtt[$key])){
	                /*$attributeData[$key]['attribute'] = $customDescDataAtt[$key];
	                $attributeData[$key]['attributevalue'] = $customDescDataAttVal[$key];
	                $attributeData[$key]['attributegroup'] = $customDescDataAttGroup[$key]; */

            		$attrname = trim($customDescDataAtt[$key]);
           			$attributeDatatmp[$attrname]['attribute'] =  $attrname;
           			$attributeDatatmp[$attrname]['attributegroup'] =  $customDescDataAttGroup[$key];
           			$attributeDatatmp[$attrname]['attributevalue'] =  $customDescDataAttVal[$key];

	               }
	               
	            }

	            $resource = $this->resourceConnection;
	            $connection = $resource->getConnection();
	            $tableName = $resource->getTableName('technicaldata'); 
	             
	            
	            if(count($attributeDatatmp)>0){
	                //delete all the records for the product and insert again. 
	                $sql = "Delete FROM " . $tableName." Where product_id = ".$productId;
	                $connection->query($sql);

	                $valuesArr = array();
	                $sqlins = "INSERT INTO " . $tableName." (product_id, attribute, attributevalue, attributegroup) values "; 
	                foreach ($attributeDatatmp as $att) {
	              
						if(isset($att['attributevalue'])){
						   $attributename = addslashes($att['attribute']);
		                   $attributename_val = addslashes($att['attributevalue']);
		                   $attributename_grp = $att['attributegroup'];
		                   $valuesArr[] = "('$productId', '$attributename', '$attributename_val', '$attributename_grp')";
						} 
	                }
	                $sqlins .= implode(',', $valuesArr);
	                $connection->query($sqlins);
	            }
            }
            

        }
		

		return $this;
	}
}