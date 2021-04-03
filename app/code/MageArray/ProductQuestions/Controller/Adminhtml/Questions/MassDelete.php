<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action;

class MassDelete extends \Magento\Backend\App\Action
{

    public function execute()
    {
        $questionIds = $this->getRequest()->getParam('questions');
        if (!is_array($questionIds) || empty($questionIds)) {
            $this->messageManager->addError(__('Please select question(s).'));
        } else {
            try {
                foreach ($questionIds as $postId) {
                    $post = $this->_objectManager
                        ->get('MageArray\ProductQuestions\Model\Questions')
                        ->load($postId);
                    $post->delete();
                }

                $this->messageManager->addSuccess(
                    __(
                        'A total of %1 record(s) have been deleted.',
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
