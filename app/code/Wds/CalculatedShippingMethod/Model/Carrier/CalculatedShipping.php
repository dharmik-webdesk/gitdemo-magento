<?php


namespace Wds\CalculatedShippingMethod\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;

class CalculatedShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    
    protected $_code = 'calculatedshipping';

    //protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;
    protected $_getProduct;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
         Json $serializer = null,
        \Magento\Catalog\Model\Product $_getProduct,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->_getProduct = $_getProduct;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = $this->_rateResultFactory->create();
        $flag = 1;
        $allItems =  $request->getAllItems();
        $shippingCaltotal=0;

        foreach ($allItems as $item) {

           $qty = "";
           $qty = $item->getQty();

           $product = $this->_getProduct->load($item->getProduct()->getId());
           

           $shipping_amount = $product->getShippingCosts();//trim($product->getData('shipping_costs'));
           if(isset($shipping_amount) && !empty($shipping_amount)){
                if($this->checkForCustomerShippingSelection($item)){
                    $shippingCaltotal = $shippingCaltotal + ($qty * $shipping_amount);
                }
            }
            unset($product['shipping_costs']);
        }
   
        if($shippingCaltotal > 0){  
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier('calculatedshipping');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('calculatedshipping');
            $method->setMethodTitle($this->getConfigData('name'));
            $amount = $shippingCaltotal;
            $method->setPrice($amount);
            $method->setCost($amount);
            $result->append($method);
            return $result;
        } else{
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier('calculatedshipping');
            $method->setCarrierTitle("Free Shipping");
            $method->setMethod('free');
            $method->setMethodTitle("Free");
            $amount = $shippingCaltotal;
            $method->setPrice($amount);
            $method->setCost($amount);
            $result->append($method);
            return $result;
        }       
    }

    public function checkForCustomerShippingSelection($item){
            $additionalOptions = array();
            if($additionalOption = $item->getOptionByCode('additional_options')):
                $additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
            endif;
            foreach ($additionalOptions as $Options){
                if($Options['label']=="Ship Collect"){
                    return false;
                }
            }
            return true;
            
    }

    /**
     * getAllowedMethods
     *
     * @param array
     */
    public function getAllowedMethods()
    {
        return ['flatrate' => $this->getConfigData('name')];
    }
}
