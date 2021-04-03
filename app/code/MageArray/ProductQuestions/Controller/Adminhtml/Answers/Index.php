<?php

namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

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
        $resultPage->setActiveMenu('MageArray_ProductQuestions::answers');
        $resultPage->addBreadcrumb(__('Answers'), __('Answers'));
        $resultPage->addBreadcrumb(__('Manage Answers'), __('Manage Answers'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Answers'));

        return $resultPage;
    }

}
