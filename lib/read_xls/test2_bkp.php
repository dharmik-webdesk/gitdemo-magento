<?php
    //  Create the Xml to POST to the Webservice
  /*
    $Xml_to_Send = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
    $Xml_to_Send .= "<Volusion_API>";
	
    $Xml_to_Send .= "</Volusion_API>";
*/
	$at=array('Availability','PhotoURL_Small','FreeShippingItem','ProductCode','ProductCondition','ProductManufacturer',
			'ProductName','ProductPrice','ProductWeight','SalePrice','StockStatus','UPC_code','Vendor_PartNo');
			
	$ad=array('true','','','9999','test est','new','tetest',
			'test product','100','0.9','10','100','999','123456');		
	$post_string='';		
	foreach($at as $k=>$d){
		$post_string .="<".$d.">".$ad[$k]."</".$d.">";
	}	
	
    $Xml_to_Send = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
    $Xml_to_Send .= "<Volusion_API><Products>";
	$Xml_to_Send .=$post_string;
    $Xml_to_Send .= "<Products></Volusion_API>";
	header('Content-Type: text/plain');	
	echo $Xml_to_Send;
	exit;
	
	
	
	
	header('Content-Type: text/plain');
	echo $post_string;
    //  Create the Header
    $url = "http://eypox.zktfd.servertrust.com/net/WebService.aspx?Login=webdesksolution@gmail.com &EncryptedPassword=E2740E98791CE0260131B7AD9DD509F9F1BC5FD7F3D110B144BC54EC5879B4F9&Import=Insert";
    $header  = "POST".$url." HTTP/1.0 \r\n";
    $header .= "MIME-Version: 1.0 \r\n";
    $header .= "Content-type: text/xml; charset=utf-8 \r\n";
    $header .= "Content-length: ".strlen($post_string)." \r\n";
    $header .= "Content-transfer-encoding: text \r\n";
    $header .= "Request-number: 1 \r\n";
    $header .= "Document-type: Request \r\n";
    $header .= "Interface-Version: Test 1.4 \r\n";
    $header .= "Connection: close \r\n\r\n";
    $header .= $Xml_to_Send;

    //  Post and Return Xml
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
	curl_setopt($ch, CURLOPT_FAILONERROR,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 150);	
	
	
	$d=curl_getinfo($ch);
	echo "<pre>";
	print_r($d);
	echo "</pre>";	
    $data = curl_exec($ch);

    //  Check for Errors
    if (curl_errno($ch)){
        print curl_error($ch);
    } else {
       curl_close($ch);
    }

    //  Display the Xml Returned on the Browser
    echo $data;
?>