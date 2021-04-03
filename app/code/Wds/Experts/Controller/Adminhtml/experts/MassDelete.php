<?php
namespace Wds\Experts\Controller\Adminhtml\experts;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $itemIds = $this->getRequest()->getParam('experts');
        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $post = $this->_objectManager->get('Wds\Experts\Model\Experts')->load($itemId);
                   
                    //skvirja customize for the delete attribute option
                    $this->deleteRecordFromAttr($post);

                    $post->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($itemIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('experts/*/index');
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