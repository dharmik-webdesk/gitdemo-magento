<?php
namespace Wds\BannerAds\Block\Adminhtml\Bannerad\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bannerad_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Banner Information'));
    }
}
