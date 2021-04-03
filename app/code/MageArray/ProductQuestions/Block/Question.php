<?php

namespace MageArray\ProductQuestions\Block;

class Question extends \Magento\Framework\View\Element\Template
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

    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    public function getQuestionLink()
    {
        return $this->getUrl('productquestions/customer/viewquestion/');
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

    protected function _prepareLayout()
    {
        if ($this->getQuestions()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'questionlist.toolbar'
            )->setCollection(
                $this->getQuestions()
            );

            $this->setChild('toolbar', $toolbar);
        }

        return parent::_prepareLayout();
    }

    public function getcustomerId()
    {
        return $this->currentCustomer->getCustomerId();
    }

    public function getQuestions()
    {
        $collection = $this->_questionFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $this->getcustomerId());
        return $collection;
    }

    public function getProduct($id)
    {
        $collection = $this->_objectManager
            ->create('Magento\Catalog\Model\Product')->load($id);
        return $collection;
    }

    public function getAnswers($qid)
    {
        $answer = $this->_answersFactory->create()->getCollection();
        $answer->addFieldToFilter('product_questions_id', $qid);
        $answer->addFieldToFilter('status', 2);
        return $answer;
    }
}