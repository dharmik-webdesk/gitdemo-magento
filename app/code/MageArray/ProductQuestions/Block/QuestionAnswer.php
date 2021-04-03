<?php

namespace MageArray\ProductQuestions\Block;

use Magento\Framework\View\Element\Template;

class QuestionAnswer extends Template
{

    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \MageArray\ProductQuestions\Model\LikedislikeFactory $likedislikeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_coreRegistry = $coreRegistry;
        $this->_questionFactory = $questionsFactory;
        $this->_answersFactory = $answersFactory;
        $this->_likedislikeFactory = $likedislikeFactory;
        $this->_objectManager = $objectManager;
    }

    public function getcustomerSession()
    {
        $customerSession = $this->_objectManager
            ->create('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
            return $customerSession->getCustomer();
        }
    }

    public function getQuestions()
    {
        $search = $this->getRequest()->getParam('search');
        $storeConfig = $this->_objectManager
            ->create('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeConfig->getStore();
        $storeId = $store->getData('store_id');
        $product = $this->_coreRegistry->registry('current_product');
        $collection = $this->_questionFactory->create()->getCollection();
        $collection->addFieldToFilter('product_id', $product->getId());
        $collection->addFieldToFilter('status', 2);
        $collection->addFieldToFilter('visibility', 'Public');
        $collection->addFieldToFilter('store_id', [
            ['finset' => ['0']],
            ['finset' => [$storeId]],
        ]);
        if ($search) {
            $collection->getSelect()->where("questions like '%$search%'");
        }

        return $collection;
    }

    public function getAnswers($id)
    {
        $collection = $this->_answersFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 2);
        $collection->addFieldToFilter('product_questions_id', $id);
        return $collection;
    }

    public function makeClickableLinks($s)
    {
        return preg_replace(
            '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
            '<a href="$1" target="_blank">$1</a>',
            $s
        );
    }

    public function whoAskQue()
    {
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/ask_questions';
        $value = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }

    public function whoGiveAns()
    {
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/answers';
        $values = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $values;
    }

    public function queVisibility()
    {
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/visibility';
        $values = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $values;
    }

    public function approveQue()
    {
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/approve_que';
        $status = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $status;
    }

    public function approveAns()
    {
        $scopeConfig = $this->_objectManager
            ->create('Magento\Framework\App\Config\ScopeConfigInterface');

        $configPath = 'productquestions/general/approve_ans';
        $ansStatus = $scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $ansStatus;
    }

    public function getQALikes($id, $type)
    {
        $countDisLike = $this->_likedislikeFactory->create()->getCollection();
        $countDisLike->addFieldToFilter('qa_id', $id);
        $countDisLike->addFieldToFilter('like', 1);
        $countDisLike->addFieldToFilter('type', $type);
        if (count($countDisLike) > 0) {
            return count($countDisLike);
        }

        return 0;
    }

    public function getQADisLikes($id, $type)
    {
        $countDisLike = $this->_likedislikeFactory->create()->getCollection();
        $countDisLike->addFieldToFilter('qa_id', $id);
        $countDisLike->addFieldToFilter('dislike', 1);
        $countDisLike->addFieldToFilter('type', $type);

        if (count($countDisLike) > 0) {
            return count($countDisLike);
        }

        return 0;
    }
}
