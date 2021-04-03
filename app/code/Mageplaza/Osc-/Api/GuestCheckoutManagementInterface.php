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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Api;

/**
 * Interface for update item information
 * @api
 */
interface GuestCheckoutManagementInterface
{
    /**
     * @param string $cartId
     * @param int $itemId
     * @param int $itemQty
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function updateItemQty($cartId, $itemId, $itemQty);

    /**
     * @param string $cartId
     * @param int $itemId
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function removeItemById($cartId, $itemId);

    /**
     * @param string $cartId
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function getPaymentTotalInformation($cartId);

    /**
     * @param string $cartId
     * @param bool $isUseGiftWrap
     * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap);

    /**
     * @param string $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param string[] $customerAttributes
     * @param string[] $additionInformation
     * @return bool
     */
    public function saveCheckoutInformation(
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    );

    /**
     * @param string $cartId
     * @param string $email
     * @return bool
     */
    public function saveEmailToQuote($cartId, $email);
}
