<?php
namespace Sivajik34\CustomFee\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helperData= $objectManager->get('Sivajik34\CustomFee\Helper\Data');
        
        $quote = $observer->getQuote();
        $CustomFeeFee = $quote->getFee();
        $CustomFeeBaseFee = $quote->getBaseFee();
        if (!$quote->getFeeAttr()) {
            return $this;
        }
        
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('fee', $CustomFeeFee);
        $order->setData('base_fee', $CustomFeeBaseFee);
        
        $CustomFeeFee = $quote->getFeeAttr();
        $lable=$helperData->getFeeLabel($CustomFeeFee);
        $order->setData('fee_attr', $lable);
        

        return $this;
    }
}
