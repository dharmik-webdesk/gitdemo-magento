<?php

namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageArray_ProductQuestions::questions');
        $resultPage->addBreadcrumb(__('Questions'), __('Questions'));
        $resultPage->addBreadcrumb(
            __('Manage Questions'),
            __('Manage Questions')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Questions'));

        return $resultPage;
    }

}
