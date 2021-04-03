<?php
namespace Wds\ReviewNotify\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
		protected $_storeManager;
		protected $_productManager;
		protected $_transportBuilder;
		protected $_scopeConfigInterface;
		protected $_stateInterface;
		
		public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,
									\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
							        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
							        \Magento\Framework\Translate\Inline\StateInterface $stateInterface,
							        \Magento\Catalog\Model\Product  $productManager,
							        \Magento\Catalog\Helper\Image $imageHelper
									)		
								
		{

			$this->_storeManager = $storeManager;
			$this->_transportBuilder = $transportBuilder;
			$this->_scopeConfigInterface = $scopeConfigInterface;
			$this->_stateInterface = $stateInterface;
			$this->_productManager = $productManager;
			$this->imageHelper = $imageHelper;
		}


		public function sendEmail($data){

			$transportBuilder = $this->_transportBuilder;
		    $inlineTranslation = $this->_stateInterface;
		    $scopeConfig = $this->_scopeConfigInterface;
		    $storeid = $this->_storeManager->getStore()->getId();
		    if($data['product_id']!=""){
			  $product = $this->_productManager->load($data['product_id']);
		      $prodname = $product->getName();
		      $produrl = $product->getProductUrl();
		      
		      if(substr($produrl, -4)=='html'){
		        $produrl=$produrl;
		      }else if(substr($produrl, -1)!='/'){
		        $produrl=$produrl.'/';
		      }

		      $produrl = $produrl.'?tracking='.$data['tracking'].'&#write_review';
		      $product_image=$this->imageHelper->init($product, 'small_image', ['type'=>'small_image'])->keepAspectRatio(true)->resize('300','300')->getUrl();
		      $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeid);

		      $templateVars = array(
		                  'name' => $data['customer_name'],
		                  'product_name' => $prodname,
		                  'product_image' => $product_image,
		                  'write_review_link' => $produrl,
		                  'message' => $data['message']
		                );
		      $sender['email'] = $scopeConfig->getValue(
		                'trans_email/ident_general/email',
		                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
		              );

		      $sender['name'] = $scopeConfig->getValue(
		                'trans_email/ident_general/name',
		                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
		              );

		      $to = array($data['customer_email']);
		      $inlineTranslation->suspend();
		      $transport = $transportBuilder->setTemplateIdentifier(8)
		              ->setTemplateOptions($templateOptions)
		              ->setTemplateVars($templateVars)
		              ->setFrom($sender)
		       		  ->addTo($to)
		         	  ->getTransport();
		       $transport->sendMessage();
		       $inlineTranslation->resume();
		  }
		  return true;
    }



}
?>