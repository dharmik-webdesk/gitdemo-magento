<?php

namespace MageArray\ProductQuestions\Controller\Customer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        
        return parent::dispatch($request);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()
            ->getTitle()->set(__('Product Questions &amp; Answers'));
        return $resultPage;

    }
}