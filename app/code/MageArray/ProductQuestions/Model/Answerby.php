<?php

namespace MageArray\ProductQuestions\Model;

class Answerby implements \Magento\Framework\Option\ArrayInterface
{

    const ADMIN = 'Admin';

    const CUSTOMER = 'Customer';

    const GUEST = 'Guest';

    public function toOptionArray()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public static function getOptionArray()
    {
        return [
            self::ADMIN => __('Admin'),
            self::CUSTOMER => __('Customer'),
            self::GUEST => __('Guest')
        ];
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