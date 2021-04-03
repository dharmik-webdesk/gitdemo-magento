<?php
namespace Wds\CustomRedirection\Observer;
use Magento\Framework\App\Filesystem\DirectoryList;
class Customredirection implements \Magento\Framework\Event\ObserverInterface{
 
	public function __construct(
					\Magento\Framework\Filesystem $filesystem,  
				    \Magento\Framework\App\Response\RedirectInterface $redirect,
				    \Magento\Framework\UrlInterface $urlBuilder,
				    \Magento\Framework\App\ResponseFactory $responseFactory) 
	{
            $this->filesystem = $filesystem;
	    $this->redirect = $redirect;
	    $this->urlBuilder = $urlBuilder;
	    $this->_responseFactory = $responseFactory;
        }

        public function execute(\Magento\Framework\Event\Observer $observer){

			
			$url = $_SERVER['HTTP_HOST'];
		if(strpos($url,$this->urlBuilder->getUrl()) !== false || strpos($url,$this->urlBuilder->getUrl()) !== false){
	 	$mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA); 
		$basepath=$mediaDirectory->getAbsolutePath('301Redirection');
		$file_path=$basepath.'/urls.json';
			if(file_exists($file_path)){
				$seach=$_SERVER['REQUEST_URI'];
				$seach=str_replace('//','/',$seach);				
				$seach=rtrim($seach,'/');
				$string = file_get_contents($file_path);
				$json_a = json_decode($string, true);
				$key = array_search($seach, $json_a['url']);
				if($key){
					$event = $observer->getEvent();
					$this->_responseFactory->create()->setRedirect($key)->sendResponse();
					exit;
				}
			}	        
		} 
	}
}
