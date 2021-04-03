<?php
namespace Sivajik34\CustomFee\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CustomFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Sivajik34\CustomFee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Sivajik34\CustomFee\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Sivajik34\CustomFee\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger

    )
    {
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $customFeeConfig = [];
        $enabled = $this->dataHelper->isModuleEnabled();
        $minimumOrderAmount = $this->dataHelper->getMinimumOrderAmount();
        
        $quote = $this->checkoutSession->getQuote();
        $subtotal = $quote->getSubtotal();
        $customFeeConfig['custom_fee']=array();
        if($this->dataHelper->getFeeLabel())
            $customFeeConfig['custom_fee'][]=array('id'=>1,'custom_fee_amount'=>$this->dataHelper->getCustomFee(),'fee_label'=>$this->dataHelper->getFeeLabel());

        for($i=2;$i<=5;$i++){
            if($this->dataHelper->getFeeLabel($i)){
             $customFeeConfig['custom_fee'][]=array('id'=>$i,'custom_fee_amount'=>$this->dataHelper->getCustomFee($i),'fee_label'=>$this->dataHelper->getFeeLabel($i));
            }
        }
        $customFeeConfig['show_hide_customfee_block'] = ($enabled && ($minimumOrderAmount <= $subtotal) && $quote->getFee()) ? true : false;
        $customFeeConfig['show_hide_customfee_shipblock'] = ($enabled && ($minimumOrderAmount <= $subtotal)) ? true : false;
        return $customFeeConfig;
    }
}
