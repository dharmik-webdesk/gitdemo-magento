<?php
namespace Wds\Warehouse\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('id');
		try {
				$banner = $this->_objectManager->get('Wds\Warehouse\Model\Warehouse')->load($id);
				
				
				$this->deleteRecordFromAttribute($banner);
				 
				 
				$banner->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
	    $this->_redirect('*/*/');
    }
	
	public function deleteRecordFromAttribute($model){
    	$expert = $model->getName();
    	
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$eavAttribute = $objectManager->create('Magento\Eav\Model\Config');
    	$attribute = $eavAttribute->getAttribute('catalog_product', 'warehouse');
    	$options = $attribute->getSource()->getAllOptions();

    	$optionsDelete = array();
        foreach ($options as $optionId => $value) {
        	
        	if($value['label'] == $expert){
        		$opi = $value['value'];
        		$optionsDelete['value'][$opi] = true;  
        		$optionsDelete['delete'][$opi] = true;
        	}

        }
       
    	$setupObject = $objectManager->create('Magento\Eav\Setup\EavSetup');
    	$setupObject->addAttributeOption($optionsDelete);


    }
}
