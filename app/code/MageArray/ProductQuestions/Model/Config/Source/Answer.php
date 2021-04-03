<?php

namespace MageArray\ProductQuestions\Model\Config\Source;

class Answer implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Anyone')],
            ['value' => '2', 'label' => __('Registered Customers')],
            ['value' => '3', 'label' => __('Only Admin')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Anyone'), 1 => __('Customers'), 2 => __('Admin')];
    }
}
