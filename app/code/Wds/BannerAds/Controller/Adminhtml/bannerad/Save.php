<?php
namespace Wds\BannerAds\Controller\Adminhtml\bannerad;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Store\Model\StoreManagerInterface;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
   protected $storeManager;
   protected $_filesystem ;
   
  public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */


	public function saveImage($btn_name,$db_name,$data,$olddbname){
    	$file_name['res']=false;
    	$file_name['error']=false;
    	$file_name['is_delete']=false;
		$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
					->getDirectoryRead(DirectoryList::MEDIA); 
		$this->_filesystem =$this->_objectManager->get('Magento\Framework\Filesystem');
		
		if(isset($_FILES[$btn_name]['name']) AND $_FILES[$btn_name]['name']!='' ){
			try{
			
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => $btn_name]
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
				
				$result = $uploader->save($mediaDirectory->getAbsolutePath('banner_ads'));
				if($result['error']==0)
					{
					
					
					$absolutePath = 
						$this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('banner_ads').$result['file'];
						
			        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('banner_ads/resize').$result['file'];         
					
				$imageResize = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
		        $imageResize->open($absolutePath);
		        $imageResize->constrainOnly(TRUE);         
		        $imageResize->keepTransparency(TRUE);         
		        $imageResize->keepFrame(FALSE);         
		        $imageResize->keepAspectRatio(TRUE); 
				if($btn_name=='banner_image_main_img')        
			        $imageResize->resize(1150);  
				else
					$imageResize->resize(650);
					  	
        		$destination = $imageResized ;    
			    $imageResize->save($destination); 
					
					
					
					
						$file_name['res'] ='banner_ads/resize'.$result['file'];
					}
			} catch (\Exception $e) {
				        $resultRedirect = $this->resultRedirectFactory->create();
				$this->messageManager->addError($e->getMessage());
				$this->_getSession()->setFormData($data);
				$file_name['error']=$resultRedirect->setPath('*/*/new');
          		  	
          		  }
		  }				

		  if(isset($data[$btn_name]['delete']) && $data[$btn_name]['delete'] == '1'){
			
			if($file_name['res']==false)
				$file_name['is_delete']=true;
			 @unlink($mediaDirectory->getAbsolutePath().$olddbname);
 			 @unlink($mediaDirectory->getAbsolutePath().'resize/'.$olddbname);
		  }

		return $file_name;
	}
    public function execute()
    {

        $data = $this->getRequest()->getPostValue();
	$this->storeManager=$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $store_data= $this->storeManager->getStores();
	$store_ids[]=0;
	foreach($store_data as $d){
		$store_ids[]=$d->getId();
	}
		
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Wds\BannerAds\Model\Bannerad');
	    $id = $this->getRequest()->getParam('id');
	   if($this->getRequest()->getParam('store_ids')){
	     $stores= $this->getRequest()->getParam('store_ids');
	     if(in_array(0,$stores)){
		$stores=implode(',', $store_ids);
	     }else{
		     $stores=implode(',', $this->getRequest()->getParam('store_ids'));		
	     }		
	     $data['store_id'] = $stores;
	   }
             $model2 = $model->getCollection();
	     if($id)
	     $model2->addFieldToFilter('id', array('neq' => $id));
             if(isset($data['store_id']))	     
		$serch_data=$model2->addStoreFilter($data['store_id']);
             if(isset($data['category_id']))	     
		     $model2->addFieldToFilter('category_id', array('eq' => $data['category_id']));
	     if($serch_data->getSize()){
		$this->messageManager->addError(__('Record is already exists'));
		$this->_getSession()->setFormData($data);
            	return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
		exit;
	     }
	   


            
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }

		$array=array('banner_image'=>'banner_image_img','banner_image2'=>'banner_image2_img','banner_image_main'=>'banner_image_main_img');
		foreach($array as $k=>$dt){
		
			if ($id) {
				$old_db_name=$model->getData($k);
			}else{
				$old_db_name='';			
			}

			$sname=$this->saveImage($dt,$k,$data,$old_db_name);
			if($sname['error']){

			  return $sname['error'];
			}else if($sname['is_delete']==1)
				$data[$k]='';
			else if($sname['res']){
			 	$data[$k]=$sname['res'];
			}
 
		}
		if(!isset($data['show_on_checkout'])){
			$data['show_on_checkout']=0;
		}
		

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Bannerad has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Bannerad.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
