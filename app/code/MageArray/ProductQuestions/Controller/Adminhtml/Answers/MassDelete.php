<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

use Magento\Backend\App\Action;

class MassDelete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $answersIds = $this->getRequest()->getParam('answers');
        if (!is_array($answersIds) || empty($answersIds)) {
            $this->messageManager->addError(__('Please select answer(s).'));
        } else {
            try {
                foreach ($answersIds as $postId) {
                    $post = $this->_objectManager
                        ->get('MageArray\ProductQuestions\Model\Answers')
                        ->load($postId);
                    $post->delete();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
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
