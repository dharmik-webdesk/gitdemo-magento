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

namespace Sivajik34\CustomFee\Block\Adminhtml\Sales\Order\Invoice;


use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

/**
 * Class GiftWrap
 * @package Mageplaza\Osc\Block\Totals\Order
 */
class PdfTotal extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * Init Totals
     */
   
     public function getTotalsForDisplay()
    {
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $this->getOrder()->getFee();
        $label=$this->getOrder()->getFeeAttr();
        if($label){
            $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
            $total = ['amount' => $amount, 'label' => $label, 'font_size' => $fontSize];
            return [$total];
        }
    }
}
