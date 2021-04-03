<?php

namespace MageArray\ProductQuestions\Model;

class Status implements \Magento\Framework\Option\ArrayInterface
{

    const STATUS_PENDING = 1;

    const STATUS_APPROVED = 2;

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
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved')
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