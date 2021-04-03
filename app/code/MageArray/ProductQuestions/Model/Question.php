<?php
namespace MageArray\ProductQuestions\Model;

class Question extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageArray\ProductQuestions\Model\QuestionsFactory $questionsFactory
    ) {
        parent::__construct($context);
        $this->_questionsFactory = $questionsFactory;
    }

    public function getOptionArray()
    {
        $question = $this->_questionsFactory->create()->getCollection();
        $question = $question->addFieldToFilter('status', 2);
        $question = $question->getData();
        $questionArray[''] = '--Please select question--';
        foreach ($question as $detail) {
            $qid = $detail['product_questions_id'];
            $questionArray[$qid] = $detail['questions'];
        }

        return $questionArray;
    }

    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}