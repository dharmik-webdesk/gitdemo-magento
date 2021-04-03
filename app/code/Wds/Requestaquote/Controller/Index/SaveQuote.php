<?php

namespace Wds\Requestaquote\Controller\Index;

use Magento\Framework\App\Action\Context;
//use Magento\Store\Model\ScopeInterface;

class SaveQuote extends \Magento\Framework\App\Action\Action
{

	protected $_transportBuilder;
	protected $_stateInterface;
	protected $_getProduct;
	protected $_formvalidaterHelper;
	protected $_masterHelper;

	public function __construct(Context $context, 
		\Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
		\Magento\Framework\Translate\Inline\StateInterface $_stateInterface,
		\Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfigInterface,
		\Magento\Catalog\Model\Product $_getProduct,
		\Wds\Coreoverride\Helper\Formvalidater $_formvalidaterHelper,
		\Wds\Coreoverride\Helper\Master $_masterHelper
	){
        $this->_transportBuilder = $_transportBuilder;
		$this->_stateInterface = $_stateInterface;
		$this->_scopeConfigInterface = $_scopeConfigInterface;
		$this->_getProduct = $_getProduct;
		$this->_formvalidaterHelper = $_formvalidaterHelper;
		$this->_masterHelper = $_masterHelper;
        parent::__construct($context);
    }

    public function execute()
    {
    	$post = $this->getRequest()->getPostValue();
    	$g_recaptcha_response = $this->getRequest()->getPost('g-recaptcha-response');
    	if(!isset($post['name']) && !isset($g_recaptcha_response)){
    		header("Location: https://www.pumpworld.com/");
    		exit;
    	}
        
        $check_rquest_url = $this->_formvalidaterHelper->varify_request_url();
        $google_captcha_validator = $this->_formvalidaterHelper->verify_google_captcha($g_recaptcha_response);
        if($google_captcha_validator == false){
            echo "true";
            return;
        } 
		
		// save Quote
		
		$quotes = $this->_objectManager->create('Wds\Requestaquote\Model\Quotes');
		$quotes->setName($post['name']);
		$quotes->setEmail($post['email']);
		$quotes->setState($post['state']);
		$quotes->setSubject($post['subject']);
		$quotes->setComment($post['comment']);
		$quotes->setProductid($post['prodId']);
		$quotes->setSku($post['sku']);
		$quotes->save();
		
		$quoteId =  $quotes->getId();
		
		
		if(!empty($quoteId)){
			echo "Your request has been sent successfully";
		}else{
			echo "There is something issue with sending data.";
		}
		
		//send Email
		
        $_currentStore = $this->_masterHelper->getCurrentStore();
		$storeid = $_currentStore->getId();
		
		$prodname = "";
		$produrl = "";
		
		if($post['prodId']!=""){
			$product = $this->_getProduct->load($_POST['prodId']);
			$prodname = $product->getName();
			$produrl = $product->getProductUrl();
		}
		
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeid);
		$templateVars = array(
							'user_name' => $post['name'],
							'user_email' => $post['email'],
			   				'state' => $post['state'],
							'product_name' => $prodname,
			   				'product_url' => $produrl,
			   				'subject' => $post['subject'],
			   				'message' => $post['comment']
							);
		
		$sender['email'] = $this->_scopeConfigInterface->getValue(
							'trans_email/ident_general/email',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						);
		$sender['name'] = $this->_scopeConfigInterface->getValue(
							'trans_email/ident_general/name',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						);
		
	   	$to = array('sales@pumpworld.com',$post['email']);
	


		//$to = array('testing@compressorworld.com');
		
		$this->_stateInterface->suspend();
		$transport = $this->_transportBuilder->setTemplateIdentifier(2)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
			->addTo($to)
	        ->addBcc('testing@compressorworld.com')			
            ->getTransport();
			
        $transport->sendMessage();
		$this->_stateInterface->resume();
		exit;
		
    }

	
	
	
}