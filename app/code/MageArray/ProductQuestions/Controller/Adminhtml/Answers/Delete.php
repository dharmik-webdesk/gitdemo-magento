<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

class Delete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('answers_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager
                    ->create('MageArray\ProductQuestions\Model\Answers');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager
                    ->addSuccess(__('Answer has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['answers_id' => $id]
                );
            }
        }
        
        // display error message
        $this->messageManager
            ->addError(__('We can\'t find a answers to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
