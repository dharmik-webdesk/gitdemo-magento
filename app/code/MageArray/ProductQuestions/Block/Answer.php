<?php

namespace MageArray\ProductQuestions\Block;

class Answer extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
        $this->_answersFactory = $answersFactory;
        $this->_questionFactory = $questionsFactory;
        $this->_objectManager = $objectManager;
    }

    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    public function getcustomerId()
    {
        return $this->currentCustomer->getCustomerId();
    }

    protected function _prepareLayout()
    {
        if ($this->getAnswers()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'answerlist.toolbar'
            )->setCollection(
                $this->getAnswers()
            );

            $this->setChild('toolbar', $toolbar);
        }
        
        return parent::_prepareLayout();
    }

    public function getAnswerLink()
    {
        return $this->getUrl('productquestions/customer/viewanswer/');
    }

    public function makeClickableLinks($s)
    {
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $s
        );
    }

    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function getAnswers()
    {
        $collection = $this->_answersFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $this->getcustomerId());

        return $collection;
    }

    public function getProduct($id)
    {
        $question = $this->_questionFactory
            ->create()->load($id);
        $collection = $this->_objectManager
            ->create('Magento\Catalog\Model\Product')
            ->load($question->getProductId());

        return $collection;
    }

    public function getQuestions($qid)
    {
        $question = $this->_questionFactory
            ->create()->load($qid);
        return $question;
    }
}