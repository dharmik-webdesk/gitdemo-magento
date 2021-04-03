<?php
namespace Wds\Experts\Controller\Adminhtml\experts;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected $_eavSetupFactory;
    protected $_storeManager;
    protected $_attributeFactory;

    

    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Wds\Experts\Model\Experts');
                $model->load($id);
                
                //skvirja customization start 
                $this->deleteRecordFromAttr($model);
                //end customization 


                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The item has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
    
    public function deleteRecordFromAttr($model){
    	$expert = $model->getName();
    	
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$eavAttribute = $objectManager->create('Magento\Eav\Model\Config');
    	$attribute = $eavAttribute->getAttribute('catalog_product', 'expert_id');
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