<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml;

abstract class Questions extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;

    protected $_questionsFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_questionsFactory = $questionsFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('MageArray_ProductQuestions::questions');
    }
}
