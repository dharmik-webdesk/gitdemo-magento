<?php
namespace Wds\FilterBuilder\Controller\Adminhtml\attrimage;

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
				$mediaDirectory->getAbsolutePath('attribute_images');
				$result = $uploader->save($mediaDirectory->getAbsolutePath('attribute_images'));
				if($result['error']==0){
					$absolutePath = 
						$this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('attribute_images').$result['file'];
					$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('attribute_images/resize').$result['file'];         
					$imageResize = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
		        	$imageResize->open($absolutePath);
		        	$imageResize->constrainOnly(TRUE);         
		        	$imageResize->keepTransparency(TRUE);         
		        	$imageResize->keepFrame(FALSE);         
		        	$imageResize->keepAspectRatio(TRUE); 
					$imageResize->resize(250);
					$destination = $imageResized ;    
			    	$imageResize->save($destination); 
					$file_name['res'] ='attribute_images/resize'.$result['file'];
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
	
	    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Wds\FilterBuilder\Model\Attrimage');
	    	$id = $this->getRequest()->getParam('id');
	   		$model2 = $model->getCollection();
	     	if($id)
	     		$model2->addFieldToFilter('id', array('neq' => $id));

			if(isset($data['option_id']))	     
		    	 $model2->addFieldToFilter('option_id', array('eq' => $data['option_id']));

		    $serch_data=$model2;

	     	if($serch_data->getSize()){
				$this->messageManager->addError(__('Record is already exists'));
				$this->_getSession()->setFormData($data);
            	return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
			}
	   		if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }

			$array=array(
							'image_url'=>'image_path'
						);
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
 			
			$model->setData($data);
			try {
                $model->save();
                $this->messageManager->addSuccess(__('The Attrimage has been saved.'));
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
                $this->messageManager->addException($e, __('Something went wrong while saving the Attrimage.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
