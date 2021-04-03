<?php

namespace MageArray\ProductQuestions\Controller\Customer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;

class Viewquestion extends Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_questionFactory = $questionsFactory;
        $this->currentCustomer = $currentCustomer;
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
        $qid = $this->getRequest()->getParam('id');
        $collection = $this->_questionFactory->create()->load($qid);
        $customer = $this->currentCustomer->getCustomerId();
        if ($customer == $collection->getCustomerId()) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Your Question View'));
            return $resultPage;
        } else {
            $this->messageManager
                ->addError(__('No such Question exists with your account.'));
            $this->_redirect('productquestions/customer/');
        }

    }
}