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

namespace Mageplaza\Osc\Model\Plugin\Quote;

use Magento\Quote\Model\Quote as QuoteEntity;

class QuoteManagement
{
    /**
     * @var \Mageplaza\Osc\Model\CheckoutRegister
     */
    protected $checkoutRegister;

    /**
     * @var \Magento\Checkout\Model\Session
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    protected $checkoutSession;

    public function __construct(\Mageplaza\Osc\Model\CheckoutRegister $checkoutRegister,
                                \Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->checkoutRegister = $checkoutRegister;
        $this->checkoutSession    = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param QuoteEntity $quote
     * @param array $orderData
     * @return array
     */
    public function beforeSubmit(\Magento\Quote\Model\QuoteManagement $subject, QuoteEntity $quote, $orderData = [])
    {
        $this->checkoutRegister->checkRegisterNewCustomer();

        /** One step check out additional data */
        $oscData = $this->checkoutSession->getOscData();

        /** Create account when checkout */
        if (isset($oscData['register']) && $oscData['register']
            && isset($oscData['password']) && $oscData['password']
        ) {
            $quote = $this->checkoutSession->getQuote();
        }

        return [$quote, $orderData];
    }
}