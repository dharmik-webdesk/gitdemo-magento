<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

class Edit extends \MageArray\ProductQuestions\Controller\Adminhtml\Questions
{

    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('product_questions_id');
        $model = $this->_questionsFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getProductQuestionsId()) {
                $this->messageManager
                    ->addError(__('This question no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

        }

        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        
        $this->_coreRegistry->register('questions_post', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
