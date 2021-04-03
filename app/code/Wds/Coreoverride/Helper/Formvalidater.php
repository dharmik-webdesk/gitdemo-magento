<?php

namespace Wds\Coreoverride\Helper;

class Formvalidater extends \Magento\Framework\App\Helper\AbstractHelper
{
		protected $objectManager;
		protected $session;
		protected $storeManager;
	
		public function __construct() {
				
		}
	
		
		/*
		 * Verifies if the captcha data via Google and redirects to the form page if the captcha does not pass.
		 * 
		 * Parameters:
		 * Gcaptcha response - the form data submitted with the request.
		 ** Example call if your form used method='get': verify_captcha($_GET['g-recaptcha-response'], "Secret key here");
		 ** Example call if your form used method='post': verify_captcha($_POST['g-recaptcha-response'], "Secret key here");
		 */
		function verify_google_captcha($gcaptcha_response, $secret = "6LcMgNAZAAAAADmjjfqTzHvLjv3rTBj5xP96yApv"){

		  //open a new curl connection
		  $ch = curl_init();
		  $url = "https://www.google.com/recaptcha/api/siteverify";
		  $fields = array(
		    "secret" => $secret,
		    "response" => $gcaptcha_response,
		  );

		  $fields_string = "";
		  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		  
		  rtrim($fields_string, '&');
		  
		  //set the url, number of POST vars, POST data
		  curl_setopt($ch,CURLOPT_URL, $url);
		  curl_setopt($ch,CURLOPT_POST, count($fields));
		  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  
		  //execute post request
		  $result = curl_exec($ch);
		  
		  //close connection
		  curl_close($ch);
		  $json_data = json_decode($result);

		  return $json_data->success;
		  
		  //If the captcha was declined, redirect the user to the same page with the query var "captcha" set to "not-entered"
			/*  if($json_data->success == false) {
			    $redirect_to = $_SERVER["HTTP_REFERER"];
				    if(!strpos($redirect_to, "?")):
				      $redirect_to.="?captcha=not-entered";
				    else:
				      $redirect_to.="&captcha=not-entered";
				    endif;
			    	header("Location: " . $redirect_to);
			    	exit;
			  } */
		}
		function varify_request_url(){
			/*$url = @parse_url($_SERVER["HTTP_REFERER"]);
			if(trim($url['host'])!= "www.pumpworld.com"){
				header("Location: " . $_SERVER["HTTP_REFERER"]);exit;
			}*/
			return;
		}
}
?>
