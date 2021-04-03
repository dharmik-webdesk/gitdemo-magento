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

namespace Mageplaza\AffiliatePro\Block\Account;

use Mageplaza\Affiliate\Model\Config\Source\Urltype;
use Mageplaza\AffiliatePro\Model\Banner\Status;

/**
 * Class Banner
 * @package Mageplaza\AffiliatePro\Block\Account
 */
class Banner extends \Mageplaza\Affiliate\Block\Account
{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Affiliate Banners'));

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAvailableBanners()
    {
        $campaigns = $this->campaignFactory->create()->getCollection()
            ->getAvailableCampaign($this->getCurrentAccount()->getGroupId(), $this->_storeManager->getWebsite()->getId())
            ->getColumnValues('campaign_id');

        $banner = $this->objectManager->create('Mageplaza\AffiliatePro\Model\Banner');
        $bannerCollection = $banner->getCollection()
            ->addFieldToFilter('campaign_id', ['in' => $campaigns])
            ->addFieldToFilter('status', Status::ENABLED);

        return $bannerCollection;
    }

    /**
     * @param $banner
     *
     * @return string
     */
    public function getLink($banner)
    {
        $url = $banner->getLink();
        $validator = new \Zend\Validator\Uri();
        if (!$validator->isValid($url)) {
            $url = $this->getUrl('affiliate/index/index');
        }

        return $this->_affiliateHelper->getSharingUrl($url, ['source' => 'banner', 'key' => $banner->getId()], Urltype::TYPE_PARAM);
    }

    /**
     * @param $banner
     *
     * @return string
     */
    public function getContentText($banner)
    {
        return $this->getBannerLink($banner, $banner->getContentHtml());
    }

    /**
     * @param $banner
     * @param $text
     *
     * @return string
     */
    public function getBannerLink($banner, $text)
    {
        $html = '<a href="' . $this->getLink($banner) . '" ' . ($banner->getRelNofollow() ? "rel='nofollow' " : '') . 'target="_blank">';
        $html .= $text;
        $html .= '</a>';

        return $html;
    }
}
