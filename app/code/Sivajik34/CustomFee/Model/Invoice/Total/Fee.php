<?php

namespace Sivajik34\CustomFee\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Fee extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $invoice->setFee(0);
        $invoice->setBaseFee(0);

        $amount = $invoice->getOrder()->getFee();
        $invoice->setFee($amount);

        $fee_attr = $invoice->getOrder()->getFeeAttr();
        $invoice->setFeeAttr($fee_attr);

        $amount = $invoice->getOrder()->getBaseFee();
        $invoice->setBaseFee($amount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getFee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getFee());

        return $this;
    }
}
