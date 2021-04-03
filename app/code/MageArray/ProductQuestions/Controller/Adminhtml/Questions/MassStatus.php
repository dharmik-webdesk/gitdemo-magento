<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action;

class MassStatus extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $questionIds = $this->getRequest()->getParam('questions');
        if (!is_array($questionIds) || empty($questionIds)) {
            $this->messageManager->addError(__('Please select question(s).'));
        } else {
            try {
                $status = (int)$this->getRequest()->getParam('status');
                foreach ($questionIds as $postId) {
                    $post = $this->_objectManager
                        ->get('MageArray\ProductQuestions\Model\Questions')
                        ->load($postId);
                    $post->setStatus($status)->save();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been updated.',
                        count($questionIds)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

        }
        
        return $this->resultRedirectFactory
            ->create()->setPath('productquestions/*/index');
    }

}
