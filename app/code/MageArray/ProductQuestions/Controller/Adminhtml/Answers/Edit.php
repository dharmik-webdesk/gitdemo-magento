<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

class Edit extends \MageArray\ProductQuestions\Controller\Adminhtml\Answers
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('answers_id');
        $model = $this->_answersFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getAnswersId()) {
                $this->messageManager
                    ->addError(__('This answer no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

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
