<?php
namespace Wds\Warehouse\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
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
     * @var \Magento\Framework\View\Result\PageFactory
     */
	public function execute()
    {
		
        $data = $this->getRequest()->getParams();
		// add warehouse name as a option of warehouse attribute 
		if(isset($data['name']) && !empty($data['name'])){

            
           $warehouse_name = $data['name'];
           $warehouse_name = trim($warehouse_name);

            $attributeInfo = $this->_attributeFactory->getCollection()
               ->addFieldToFilter('attribute_code',['eq'=>"warehouse"])
               ->getFirstItem();
            $optionsex = $attributeInfo->getSource()->getAllOptions();
            $opexists = 0;
            $options_exists = array();
            if(count($optionsex)>0){
                foreach ($optionsex as $opvalue) {
                    if($opvalue['label'] == $warehouse_name){
                        $opexists = 1;
                    }
                }
            }
            
           

            if($opexists == 0){
                $attribute_id = $attributeInfo->getAttributeId();

                $option=array();
                $option['attribute_id'] = $attributeInfo->getAttributeId();
                
                $allStores = $this->_storeManager->getStores($withDefault = false);
                $option['value'][$warehouse_name][0] = $warehouse_name;
                foreach($allStores as $store){
                    $option['value'][$warehouse_name][$store->getId()] = $warehouse_name;
                }

                $eavSetup = $this->_eavSetupFactory->create();
                $eavSetup->addAttributeOption($option);
            }
        }
		
        if ($data) {
            $model = $this->_objectManager->create('Wds\Warehouse\Model\Warehouse');
		
            try{

				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'image']
				);

				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
					->getDirectoryRead(DirectoryList::MEDIA);

				$result = $uploader->save($mediaDirectory->getAbsolutePath('warehouse'));
					if($result['error']==0)
					{	
						$data['image'] = 'warehouse' . $result['file'];
					}
			} catch (\Exception $e) {
				
            }
			
			if(isset($data['image']['delete']) && $data['image']['delete'] == '1')
				$data['image'] = '';
			
            $model->setData($data);
			
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Warehouse has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            return;
        }
        $this->_redirect('*/*/');
    }
}
