<?php
namespace MageArray\ProductQuestions\Block\Adminhtml\Questions\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('questions_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Question Information'));
    }

    protected function _prepareLayout()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Question Information'),
                'title' => __('Question Information'),
                'content' => $this->getLayout()
                    ->createBlock('MageArray\ProductQuestions\Block\Adminhtml\Questions\Edit\Tab\Newquestion')
                    ->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'productgrid',
            [
                'label' => __('Select Product'),
                'url' => $this->getUrl(
                    'productquestions/*/productgrid',
                    ['_current' => true]
                ),
                'class' => 'ajax',
            ]
        );
    }
}
