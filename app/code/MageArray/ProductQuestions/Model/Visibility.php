<?php

namespace MageArray\ProductQuestions\Model;

class Visibility implements \Magento\Framework\Option\ArrayInterface
{

    const VISIBILITY_PUBLIC = 'Public';

    const VISIBILITY_PRIVATE = 'Private';

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
            self::VISIBILITY_PUBLIC => __('Public'),
            self::VISIBILITY_PRIVATE => __('Private')
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