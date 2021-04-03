<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

class Delete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('product_questions_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager
                    ->create('MageArray\ProductQuestions\Model\Questions');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager
                    ->addSuccess(__('Question has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['product_questions_id' => $id]
                );
            }
        }
        
        // display error message
        $this->messageManager
            ->addError(__('We can\'t find a question to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
