<?php

namespace MageArray\ProductQuestions\Model;

class Customer extends \Magento\Framework\View\Element\Template implements
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
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_customerFactory = $customerFactory;
    }

    public function getOptionArray()
    {
        $customer = $this->_customerFactory->create();
        $customerDetail = $customer->getCollection();
        $custArray[''] = "--Please Select Customer--";
        foreach ($customerDetail as $detail) {
            $name = $detail['firstname'] . ' ' . $detail['lastname'];
            $eid = $detail['entity_id'];
            $custArray[$eid] = $name . " < " . $detail['email'] . " >";
        }
        
        return $custArray;
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