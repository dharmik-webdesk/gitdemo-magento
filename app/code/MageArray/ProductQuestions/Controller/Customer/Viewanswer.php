<?php

namespace MageArray\ProductQuestions\Controller\Customer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Action\Action;

class Viewanswer extends Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->currentCustomer = $currentCustomer;
        $this->_questionFactory = $questionsFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_answersFactory = $answersFactory;
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
        $collection = $this->_answersFactory->create()->getCollection();
        $collection = $collection
            ->addFieldToFilter('product_questions_id', $qid);
        $answer = [];
        foreach ($collection as $val) {
            $answer[] = $val->getCustomerId();
        }

        $customer = $this->currentCustomer->getCustomerId();
        if (in_array($customer, $answer)) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Your Answer View'));
            return $resultPage;
        } else {
            $this->messageManager
                ->addError(__('No such Answer exists with your account.'));
            $this->_redirect('productquestions/customer/');

        }

    }
}