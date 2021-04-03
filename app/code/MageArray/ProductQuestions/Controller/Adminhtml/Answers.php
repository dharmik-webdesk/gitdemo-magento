<?php
namespace MageArray\ProductQuestions\Controller\Adminhtml;

abstract class Answers extends \Magento\Backend\App\Action
{
    protected $_answersFactory;

    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageArray\ProductQuestions\Model\AnswersFactory $answersFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_answersFactory = $answersFactory;
        $this->_escaper = $escaper;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('MageArray_ProductQuestions::answers');
    }
}
