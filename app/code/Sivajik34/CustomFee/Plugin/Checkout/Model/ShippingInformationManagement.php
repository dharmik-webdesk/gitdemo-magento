<?php
namespace Sivajik34\CustomFee\Plugin\Checkout\Model;


class ShippingInformationManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Sivajik34\CustomFee\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Sivajik34\CustomFee\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Sivajik34\CustomFee\Helper\Data $dataHelper
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        if($addressInformation->getExtensionAttributes())
            $customFee = $addressInformation->getExtensionAttributes()->getFee();
        else
            $customFee='';

        $quote = $this->quoteRepository->getActive($cartId);
        if ($customFee) {
            $fee = $this->dataHelper->getCustomFee($customFee);
            $quote->setFee($fee);
            $quote->setFeeAttr($customFee);
        } else {
            $quote->setFee(NULL);
            $quote->setFeeAttr(NULL);
        }
    }
}

