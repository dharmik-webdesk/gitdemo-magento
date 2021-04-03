<?php

namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

class NewAction extends \MageArray\ProductQuestions\Controller\Adminhtml\Answers
{
    public function execute()
    {

        $model = $this->_answersFactory->create();
        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }
        
        $this->_coreRegistry->register('answers_post', $model);
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
