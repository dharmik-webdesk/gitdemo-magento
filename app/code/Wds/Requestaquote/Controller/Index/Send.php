<?php

namespace Wds\Requestaquote\Controller\Index;

use Magento\Framework\App\Action\Context;
//use Magento\Store\Model\ScopeInterface;

class Send extends \Magento\Framework\App\Action\Action
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
		
		//send Email
		$_currentStore = $this->_masterHelper->getCurrentStore();
		$storeid = $_currentStore->getId();
		
		
		$templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeid);
		$templateVars = array(
								"CompanyName" => $post['CompanyName'],
								"Name" => $post['Name'],
								"Phone" => $post['Phone'],
								"Email" => $post['Email'],
								"Address" => $post['Address'],
								"Comment" => $post['Comment'],
							 );
		
		$sender['email'] = $this->_scopeConfigInterface->getValue(
							'trans_email/ident_general/email',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						);
		$sender['name'] = $this->_scopeConfigInterface->getValue(
							'trans_email/ident_general/name',
							\Magento\Store\Model\ScopeInterface::SCOPE_STORE
						);
		
	   	$to = array('matt@compressorworld.com');
	   	
		//$to = array('testing.webdesksolution@gmail.com');
		$this->_stateInterface->suspend();
		$transport = $this->_transportBuilder->setTemplateIdentifier(10)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
			->addTo($to)
	        ->addBcc('testing@compressorworld.com')			
            ->getTransport();
			
        $transport->sendMessage();
		$this->_stateInterface->resume();


	    $to = $post['Email'];
		$this->_stateInterface->suspend();
		$transport = $this->_transportBuilder->setTemplateIdentifier(11 )
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($sender)
			->addTo($to)
	        ->getTransport();
		$transport->sendMessage();
		$this->_stateInterface->resume();


		

		
		$this->messageManager->addSuccessMessage(
                __('Thanks for the service request. We\'ll respond to you very soon.')
            );
           return $this->resultRedirectFactory->create()->setPath('schedule-service');
    }

	
	
	
}