<?php

namespace Wds\MngWweshipping\Model\Carrier;
 
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\App\Filesystem\DirectoryList;

 
class Wdsmngwweshipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'wdsmngwweshipping';
	protected $soap_request = '';
	protected $soap_response = '';
	protected $objectManager ='';
	protected $log_path='';
	protected $wwe_main_array ='';
	protected $_directoryList;
	protected $_getProduct;
	protected $_cart;
	protected $_regionFactory;
	protected $_warehouse;
 
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $_directoryList,
        \Magento\Catalog\Model\Product $_getProduct,
        \Magento\Checkout\Model\Cart $_cart,
        \Magento\Directory\Model\RegionFactory $_regionFactory,
        \Wds\Warehouse\Model\Warehouse $_warehouse,
        array $data = []

    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_directoryList = $_directoryList;
        $this->_getProduct = $_getProduct;
        $this->_cart = $_cart;
        $this->_regionFactory = $_regionFactory;
        $this->_warehouse = $_warehouse;
		
		parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }
 
    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        
        return ['standard' => 'Standard','free_shipping' => 'Free Shipping'];
    }
 
    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
		
        //if (!$this->getConfigFlag('active')) {
            return false;
      //  }
		
       
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $flag = 1;
		$allItems =  $request->getAllItems();
        foreach ($allItems as $item) {
		   $qty = $item->getQty();
		   $weight=$item->getWeight();
		   
		   
           $_product = $this->_getProduct->load($item->getProduct()->getId());
           if($_product->getData('wwe_free_shipping') && $flag != 'no_free'){
				$flag='free';
		   }else{
				 $flag = 'no_free';	
		   }
		}
		
		/* Add Code to set free shipping: start */
	
		$subTotal = $this->_cart->getQuote()->getSubtotal();
		if($subTotal>50)
			$flag='free';
		/* Add Code to set free shipping: End */
		
		//$flag='free';
		
		if($flag=='free'){	
            $freeShippingRate = $this->_getFreeShippingRate();
            $result->append($freeShippingRate);
		} else{
			
			$this->_setShippingData($request, $flag);
			
			if($this->wwe_main_array){
				foreach($this->wwe_main_array as $value){
					$get_response=$this->_setWweShippingMethods($value);
					$result->append($get_response);
				}
			}
		}     
        
         
      
 
        return $result;
    }
	
	
	protected function _setWweShippingMethods($value) {
		$method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $name=$value['name'];
	$name=strtolower($name);
	$name=str_replace(' ','_',$name);
	$name=str_replace('@','_',$name);
	$name=str_replace('__','_',$name);
        if(strlen($name)>15)
            $name=substr($name,0,15);        

	$method->setMethod($name);
        $method->setMethodTitle($value['name']." (".$value['day']." Days)");
        $method->setPrice($value['rate']);
        $method->setCost($value['rate']);
		return $method;
    }
	
	protected function _getFreeShippingRate(){
		$method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod('free_shipping');
        $method->setMethodTitle('Free Shipping');
        $method->setPrice(0);
        $method->setCost(0);
        return $method;
    }
	
	protected function _setShippingData($request,$flag){
		
		$total_weight=0;
		
		
		$region = $this->_regionFactory->create();
		 
		$var_item_list='';
		//$wwe_main_array = array();
		$temp = true;
		foreach ($request->getAllItems() as $_item) {
			$_product = $this->_getProduct->load($_item->getProduct()->getId());
			$is_free_shipping = $_product->getData('wwe_free_shipping');
			if($is_free_shipping!=1){
				
				// Get Product qty and total weight
			    $total_weight=$_item->getQty()*ceil($_item->getWeight());
				$var_item_list .="<wwex:wsLineItem>
                           			<wwex:lineItemClass>60</wwex:lineItemClass>
                           			<wwex:lineItemWeight>".$total_weight."</wwex:lineItemWeight>
                           			<wwex:lineItemDescription>0</wwex:lineItemDescription>
                           			<wwex:lineItemPieceType>Other</wwex:lineItemPieceType>
                          			<wwex:piecesOfLineItem>".$_item->getQty()."</wwex:piecesOfLineItem>
                        			</wwex:wsLineItem>";
				
				// Get Warehouse/Sender Information	
				$WarehouseName = $_product->getResource()->getAttribute('warehouse')->getFrontend()->getValue($_product);
				// if warehouse is not seclet at specific product
				if($WarehouseName=="No"){
							$senderState=$request->getRegionId();
							$senderState=$region->load($senderState);
							
							$senderState=$senderState->getData('code');
							$senderZip=$request->getPostcode();
							$senderCountryCode=$request->getCountryId();
				}
				else
				{	
					$WarehouseCollection = $this->_warehouse->getCollection();
					foreach($WarehouseCollection as $Warehouse){
						if($Warehouse['name']==$WarehouseName){
							if($Warehouse['status']=="Enabled"){
									$senderState=$Warehouse['state'];
									$senderState=$region->load($senderState);
									$senderState=$senderState->getData('code');
									
									$senderZip=$Warehouse['zip_code'];
									$senderCountryCode=$Warehouse['country'];
							}
							else
							{
								$senderState=$request->getRegionId();
								$senderState=$region->load($senderState);
								
								$senderState=$senderState->getData('code');
								$senderZip=$request->getPostcode();
								$senderCountryCode=$request->getCountryId();
							}
						}
					}
				}
				
				// Get Receiver/customer Information
				$receiverCity = $request->getDestCity();
				$receiverZip = $request->getDestPostcode();
				$receiverCountryCode = $request->getDestCountryId();
				
				$receiverState=$request->getDestRegionId();
				if($receiverState){
					$region=$regionFactory->create();
					$receiverState=$region->load($receiverState);
					$receiverState=$receiverState->getData('code');
				}else{
					$receiverState=$request->getDestRegionCode();
					$region=$regionFactory->create();
					$receiverState=$region->loadByName($receiverState,$receiverCountryCode);
					$receiverState=$receiverState->getData('code');
				}
				
				
				if($total_weight==0){
					$log="Applying Free Shipping: ".$total_weight."(Weight) | ".$receiverCity."(City) | ".$receiverState."(State) | ".$receiverZip."(PostCode) | ".$receiverCountryCode."(Country): ";
					$this->mngAddLog( $log,true);
					$this->soap_request=false;
					return  $this->soap_request;
				}
				$log="Sending Information to WWE: ".$total_weight."(Weight) | ".$receiverCity."(City) | ".$receiverState."(State) | ".$receiverZip."(PostCode) | ".$receiverCountryCode."(Country): ";
				$this->mngAddLog( $log,true);
		
				$this->soap_request ='<?xml version="1.0"?>
				<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wwex="http://www.wwexship.com">
				   <soapenv:Header>
					<wwex:AuthenticationToken>
						<wwex:loginId>'.$this->getConfigData('wwe_login_id').'</wwex:loginId>
						<wwex:password>'.$this->getConfigData('wwe_password').'</wwex:password>
						<wwex:licenseKey>'.$this->getConfigData('wwe_license_key').'</wwex:licenseKey>
						<wwex:accountNumber>'.$this->getConfigData('wwe_account_number').'</wwex:accountNumber>	
					</wwex:AuthenticationToken>
				   </soapenv:Header>
				   <soapenv:Body>
					  <wwex:quoteSpeedFreightShipment>
						 <wwex:freightShipmentQuoteRequest>
						   <!-- <wwex:senderCity>atlanta</wwex:senderCity>-->
							<wwex:senderState>'.$senderState.'</wwex:senderState>
							<wwex:senderZip>'.$senderZip.'</wwex:senderZip>
							<wwex:senderCountryCode>'.$senderCountryCode.'</wwex:senderCountryCode>
							<wwex:receiverCity>'.$receiverCity.'</wwex:receiverCity>
							<wwex:receiverState>'.$receiverState.'</wwex:receiverState>
							<wwex:receiverZip>'.$receiverZip.'</wwex:receiverZip>
							<wwex:receiverCountryCode>'.$receiverCountryCode.'</wwex:receiverCountryCode>
							<wwex:commdityDetails>
							   <wwex:is11FeetShipment>?</wwex:is11FeetShipment>
							   <wwex:handlingUnitDetails>
								  <!--Zero or more repetitions:-->
								  <wwex:wsHandlingUnit>
									 <wwex:typeOfHandlingUnit>Other</wwex:typeOfHandlingUnit>
									 <wwex:numberOfHandlingUnit>0</wwex:numberOfHandlingUnit>
									 <wwex:lineItemDetails>
										<!--Zero or more repetitions:-->
										'.$var_item_list.'
									 </wwex:lineItemDetails>
								  </wwex:wsHandlingUnit>
							   </wwex:handlingUnitDetails>
							</wwex:commdityDetails>
						 </wwex:freightShipmentQuoteRequest>
					  </wwex:quoteSpeedFreightShipment>
				   </soapenv:Body>
				</soapenv:Envelope>';
				
				if($temp){ // for first product(item) in cart
						$this->_sendRequestToWWE($flag);
						if($this->soap_response){
							foreach($this->soap_response as $res){
								$this->wwe_main_array[$res['carrierName']]['name'] = $res['carrierName'];
								$this->wwe_main_array[$res['carrierName']]['rate'] = $res['totalPrice'];
								$this->wwe_main_array[$res['carrierName']]['day'] = $res['transitDays'];
							}
						}
						$temp = false;
				}else{	// other products(item) in cart
						$this->_sendRequestToWWE($flag);
						$temp_wwe_array = array();
						
						if($this->soap_response){
							foreach($this->soap_response as $res){
								$temp_wwe_array[$res['carrierName']]['name'] = $res['carrierName'];
								$temp_wwe_array[$res['carrierName']]['rate'] = $res['totalPrice'];
								$temp_wwe_array[$res['carrierName']]['day'] = $res['transitDays'];
							}
						}
						
							// Main wwe array compare with other warehouse array for find unique methods from each warehouse
							foreach($this->wwe_main_array as $key => $value){
									
								if (array_key_exists($key,$temp_wwe_array)){
									$rate_total = $this->wwe_main_array[$key]['rate'] + $temp_wwe_array[$key]['rate'];
									$this->wwe_main_array[$key]['rate'] = $rate_total;
									
									if($this->wwe_main_array[$key]['day'] < $temp_wwe_array[$key]['day'])
											$this->wwe_main_array[$key]['day'] = $temp_wwe_array[$key]['day'];
								}
								else{
									unset($this->wwe_main_array[$key]);
								}
							}
				}
			}
    	}
	
    }
	
	
	protected function _sendRequestToWWE($is_free=''){
     	$header = array(
	    	"Content-type: application/xml;charset=\"utf-8\"",
	    	"Accept: application/xml",
	    	"Cache-Control: no-cache",
	    	"Pragma: no-cache",
	    	"SOAPAction: \"run\"",
	    	"Content-length: ".strlen($this->soap_request),
	  		"Connection: close"
  		);
   //exit;
		$soap_do = curl_init();
  		curl_setopt($soap_do, CURLOPT_URL, "http://www.wwexship.com/webServices/services/SpeedFreightShipment" );
  		curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, 1); 
  		curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
  		curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
  		curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
  		curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
  		curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
  		curl_setopt($soap_do, CURLOPT_POST,           true );
  		curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $this->soap_request);
  		curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);
  		$result= curl_exec ($soap_do);
		if($result === false) {
			$err = 'Curl error: ' . curl_error($soap_do);
			$log=$err;
			$this->mngAddLog( $log );
    		curl_close($soap_do);
    		//print $err;
			$this->soap_response=false;
  		} else {
  			curl_close($soap_do);
    		$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $result);
			$xml = simplexml_load_string($xml);
			$json = json_encode($xml);
			$responseArray = json_decode($json,true);
			$response111=$responseArray['soapenvBody']['quoteSpeedFreightShipmentResponse']['quoteSpeedFreightShipmentReturn'];
			$status=$response111['responseStatusDescription'];
			
			if($status=='Failed'){
				$response111_e=$response111['errorDescriptions']['freightShipmentErrorDescription']['errorDescription'];
				$log="\n\n Error:".$status.'('.$response111_e.')'."\n\n".'**************************************'."\n\n";
				$this->mngAddLog( $log );
			    $this->soap_response=false;
			}else{
				if($is_free!='free'){
					$log=$status."\n\n".'**************************************'."\n\n";
					$this->mngAddLog( $log );
				}else{
					$status='Success';
					$log=$status."\n\n".'**************************************'."\n\n";
					$this->mngAddLog( $log );
				}
				
				$response=$responseArray['soapenvBody']['quoteSpeedFreightShipmentResponse']['quoteSpeedFreightShipmentReturn'];
				$methods=$response['freightShipmentQuoteResults']['freightShipmentQuoteResult'];
				$this->soap_response=$methods;
				
			}
			
		}	
    }
	
	public function mngAddLog( $log,$first='' ) {

		$this->log_path=$this->_directoryList->getPath(DirectoryList::LOG);
		$base=$this->log_path.'/checkoutLog.txt';
		$f=fopen($base,'a+');
		if($first)
			$log=date('d-m-y H:i:s').' ['.$_SERVER['REMOTE_ADDR'].']'.': '.$log;
		fwrite($f,$log);
		fclose($f);
	}
}