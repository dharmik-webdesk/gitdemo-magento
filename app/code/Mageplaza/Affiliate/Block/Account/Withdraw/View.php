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
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Block\Account\Withdraw;

use Mageplaza\Affiliate\Block\Account\Withdraw;

/**
 * Class View
 * @package Mageplaza\Affiliate\Block\Account\Withdraw
 */
class View extends Withdraw
{
    /**
     * @return mixed
     */
    public function getWithdraw()
    {
        return $this->registry->registry('withdraw_view_data');
    }

    /**
     * @return mixed
     */
    public function getPaymentDetail()
    {
        $withdraw = $this->getWithdraw();

        return $withdraw->getPaymentModel()->getPaymentDetail();
    }
}