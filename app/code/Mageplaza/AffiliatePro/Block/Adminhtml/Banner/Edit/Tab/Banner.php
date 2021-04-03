<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AffiliatePro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AffiliatePro\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\AffiliatePro\Model\Banner\Campaign;
use Mageplaza\AffiliatePro\Model\Banner\Status;

/**
 * Class Banner
 * @package Mageplaza\AffiliatePro\Block\Adminhtml\Banner\Edit\Tab
 */
class Banner extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @var \Mageplaza\AffiliatePro\Model\Banner\Status
     */
    protected $_status;

    /**
     * @var \Mageplaza\AffiliatePro\Model\Banner\Campaign
     */
    protected $_campaign;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfigModel;

    /**
     * Banner constructor.
     *
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param \Mageplaza\AffiliatePro\Model\Banner\Status $status
     * @param \Mageplaza\AffiliatePro\Model\Banner\Campaign $campaign
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        Status $status,
        Campaign $campaign,
        Config $wysiwygConfig,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_yesno = $yesno;
        $this->_status = $status;
        $this->_campaign = $campaign;
        $this->_wysiwygConfigModel = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\Affiliate\Model\Banner $banner */
        $banner = $this->_coreRegistry->registry('current_banner');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Banner Information'),
            'class'  => 'fieldset-wide'
        ]);

        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => __('Title'),
            'title'    => __('Title'),
            'required' => true,
        ]);
        $fieldset->addField('content', 'editor', [
            'name'         => 'content',
            'label'        => __('Content'),
            'title'        => __('Content'),
            'config'       => $this->_wysiwygConfigModel->getConfig(['add_variables' => false])->addData(['add_widgets' => false]),
            'wysiwyg'      => false,
            'container_id' => 'content',
        ]);
        $fieldset->addField('link', 'text', [
            'name'  => 'link',
            'label' => __('Redirect Url'),
            'title' => __('Redirect Url'),
            'note'  => __('If empty, home page will be used.')
        ]);
        $fieldset->addField('campaign_id', 'select', [
            'name'     => 'campaign_id',
            'label'    => __('Related Campaign'),
            'title'    => __('Related Campaign'),
            'required' => true,
            'values'   => $this->_campaign->toOptionArray(),
            'note'     => __('Only affiliates who are in above campaign can see this banner.')
        ]);
        $fieldset->addField('rel_nofollow', 'select', [
            'name'     => 'rel_nofollow',
            'label'    => __('Rel Nofollow'),
            'title'    => __('Rel Nofollow'),
            'required' => true,
            'values'   => $this->_yesno->toOptionArray(),
            'note'     => __('Put the rel="nofollow" attribute on the link.')
        ]);
        $fieldset->addField('status', 'select', [
            'name'     => 'status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'required' => true,
            'values'   => $this->_status->toOptionArray()
        ]);

        $form->addValues($banner->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Banner Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
