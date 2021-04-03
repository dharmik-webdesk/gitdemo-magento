<?php
namespace Wds\Experts\Controller\Adminhtml\experts;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;



class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    /*public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }*/
    protected $_eavSetupFactory;
    protected $_storeManager;
    protected $_attributeFactory;

    public function __construct(
        Action\Context $context,        
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_storeManager = $storeManager;
        $this->_attributeFactory = $attributeFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        //skvirja customizing for adding the expert name as attribute option in attribute: expert_id
        $data = $this->getRequest()->getPostValue();
        
        if(isset($data['name']) && !empty($data['name'])){

            
           $expert_name = $data['name'];
           $expert_name = trim($expert_name);

            $attributeInfo = $this->_attributeFactory->getCollection()
               ->addFieldToFilter('attribute_code',['eq'=>"expert_id"])
               ->getFirstItem();
            $optionsex = $attributeInfo->getSource()->getAllOptions();
            $opexists = 0;
            $options_exists = array();
            if(count($optionsex)>0){
                foreach ($optionsex as $opvalue) {
                    if($opvalue['label'] == $expert_name){
                        $opexists = 1;
                    }
                }
            }
            
           

            if($opexists == 0){
                $attribute_id = $attributeInfo->getAttributeId();

                $option=array();
                $option['attribute_id'] = $attributeInfo->getAttributeId();
                
                $allStores = $this->_storeManager->getStores($withDefault = false);
                $option['value'][$expert_name][0] = $expert_name;
                foreach($allStores as $store){
                    $option['value'][$expert_name][$store->getId()] = $expert_name;
                }

                $eavSetup = $this->_eavSetupFactory->create();
                $eavSetup->addAttributeOption($option);
            }

            

        }
        // end skvirja customization

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Wds\Experts\Model\Experts');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }
$custom_flage=0;
if(isset($_FILES['photo']['name']) AND $_FILES['photo']['name']!='' ){
			try{

				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'photo']
				);

				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
					->getDirectoryRead(DirectoryList::MEDIA);

                    //echo $mediaDirectory->getAbsolutePath('experts');exit;
				$result = $uploader->save($mediaDirectory->getAbsolutePath('experts'));
					if($result['error']==0)
					{	
						$data['photo'] = 'experts' . $result['file'];
					}
			} catch (\Exception $e) {
				//unset($data['image']);
                /*echo '<pre>';
                print_R($e->getMessage());
                exit;*/

            }
$custom_flage=1;
}
			//var_dump($data);die;
			if(isset($data['photo']['delete']) && $data['photo']['delete'] == '1'){
				$data['photo'] = ''; $custom_flage=2;
			}
	    if($custom_flage==0)
		unset($data['photo']);
			
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Experts has been saved.'));
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
                $this->messageManager->addException($e, __('Something went wrong while saving the Experts.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
