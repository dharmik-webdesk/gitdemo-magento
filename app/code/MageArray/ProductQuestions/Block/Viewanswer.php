<?php

namespace MageArray\ProductQuestions\Block;

class Viewanswer extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
        $this->_questionFactory = $questionsFactory;
        $this->_objectManager = $objectManager;
        $this->_answersFactory = $answersFactory;
    }


    public function makeClickableLinks($s)
    {
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $s
        );
    }

    public function getBackUrl()
    {
        return $this->getUrl('productquestions/customer/');
    }

    public function getcustomerId()
    {
        return $this->currentCustomer->getCustomerId();
    }

    public function getQuestions()
    {
        $qid = $this->getRequest()->getParam('id');
        $collection = $this->_questionFactory->create()->load($qid);
        return $collection;
    }

    public function getProduct()
    {
        $qid = $this->getRequest()->getParam('id');
        $question = $this->_questionFactory->create()->load($qid);
        $collection = $this->_objectManager
            ->create('Magento\Catalog\Model\Product')
            ->load($question->getProductId());
        return $collection;
    }

    public function getAnswer()
    {
        $qid = $this->getRequest()->getParam('id');
        $question = $this->_questionFactory->create()->load($qid);
        $collection = $this->_answersFactory->create()->getCollection();
        $collection->addFieldToFilter(
            'product_questions_id', $question->getProductQuestionsId()
        );
        return $collection;
    }
}