<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

class NewAction extends
    \MageArray\ProductQuestions\Controller\Adminhtml\Questions
{
    public function execute()
    {
        $model = $this->_questionsFactory->create();
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
