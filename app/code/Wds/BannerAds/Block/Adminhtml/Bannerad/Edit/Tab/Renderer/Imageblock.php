<?php
namespace Wds\BannerAds\Block\Adminhtml\Bannerad\Edit\Tab\Renderer;
use Magento\Framework\DataObject;
use Magento\Framework\Object;
use Magento\Store\Model\StoreManagerInterface;
class Imageblock  extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
        private $_storeManager;
    	
	public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storemanager, array $data = []){
	        $this->_storeManager = $storemanager;
	        parent::__construct($context, $data);
	        $this->_authorization = $context->getAuthorization();
   	}

	public function render(DataObject $row){
		$image=$row->getBannerImageMain();
		if($image){
			$mediaDirectory = $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$image ='<img width="150" src="'.$mediaDirectory.''.$image.'" />' ;
		}

		return $image;	
	}
}

?>
