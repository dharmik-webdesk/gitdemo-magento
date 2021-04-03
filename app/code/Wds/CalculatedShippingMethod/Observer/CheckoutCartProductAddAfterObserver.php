<?php

namespace Wds\CalculatedShippingMethod\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;



class CheckoutCartProductAddAfterObserver implements ObserverInterface
{

    protected $_request;
    protected $serializer;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request, Json $serializer = null){
            $this->_request = $request;
            $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /* @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getQuoteItem();

        $additionalOptions = array();

        if ($additionalOption = $item->getOptionByCode('additional_options')){
            $additionalOptions = (array) $this->serializer->unserialize($additionalOption->getValue());
        }

        $post = array();
        if($this->_request->getParam('shipping-option')=="customer-carrier"):
             $carrier_name = $this->_request->getParam('user_carrier_name');
             if($carrier_name!=""):
                    $post = array('Ship Collect'=> "Your carrier: ".$carrier_name);
             endif;
        else:
            if($this->_request->getParam('shipping-option')):
                $post = array('Carrier Shipping Cost'=> $this->_request->getParam('shipping-option'));
            endif; 
        endif;

        //echo $post;exit;
        if(is_array($post)){
            foreach($post as $key => $value){
                if($key == '' || $value == ''){
                    continue;
                }

                $additionalOptions[] = array(
                    'label' => $key,
                    'value' => $value
                );
            }
        }
       
        if(count($additionalOptions) > 0){
            $item->addOption(array(
                'code' => 'additional_options',
                'value' => $this->serializer->serialize($additionalOptions)
            ));
        }


    }
   
}
