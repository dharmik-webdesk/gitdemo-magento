<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

use Magento\Backend\App\Action;

class MassStatus extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $answersIds = $this->getRequest()->getParam('answers');
        if (!is_array($answersIds) || empty($answersIds)) {
            $this->messageManager->addError(__('Please select answer(s).'));
        } else {
            try {
                $status = (int)$this->getRequest()->getParam('status');
                foreach ($answersIds as $postId) {
                    $post = $this->_objectManager
                        ->get('MageArray\ProductQuestions\Model\Answers')
                        ->load($postId);
                    $post->setStatus($status)->save();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been updated.',
                        count($answersIds)
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
