<?php
    /**
     * Hello Rewrite Product View Controller
     *
     * @category    Webkul
     * @package     Webkul_Hello
     * @author      Webkul Software Private Limited
     *
     */
namespace Wds\Coreoverride\Controller\Cart;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Checkout\Controller\Cart\Add
{
    
    
   
    
/**
 * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
 */
public function execute()
{
    if (!$this->_formKeyValidator->validate($this->getRequest())) {
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    $params = $this->getRequest()->getParams();
    
    try {
        if (isset($params['qty'])) {
            $filter = new \Zend_Filter_LocalizedToNormalized(
                ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
            );
            $params['qty'] = $filter->filter($params['qty']);
        }
       
        $product = $this->_initProduct();
        $related = $this->getRequest()->getParam('related_product');

        /**
         * Check product availability
         */
        if (!$product) {
            return $this->goBack();
        }

        $this->cart->addProduct($product, $params);
        if (!empty($related)) {
            $this->cart->addProductsByIds(explode(',', $related));
        }

        $this->cart->save();

        /**
         * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
         */
        $this->_eventManager->dispatch(
            'checkout_cart_add_product_complete',
            ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
        );

        if (!$this->_checkoutSession->getNoCartRedirect(true)) {
            //if (!$this->cart->getQuote()->getHasError() && !isset($params['from_product'])) {
            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $product->getName()
                );
                $this->messageManager->addSuccessMessage($message);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $customerSession = $objectManager->get('\Magento\Customer\Model\Session');
                $customerSession->setCustomMessage($message);
                
                
            }
            if(isset($params['options'])){
               
                $options = count($params['options']);
                if($options>0){
                    
                    if ($this->getRequest()->isAjax()) {
    
                            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                                'backUrl' => $this->_url->getUrl('checkout', array('_secure' => true)),
                            ]);
                    }
                    return $this->resultRedirectFactory->create()->setPath('checkout');
                }
            }
            if(isset($params['additional_options'])==1){

                    if ($this->getRequest()->isAjax()) {
    
                            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
                                'backUrl' => $this->_url->getUrl('checkout', array('_secure' => true)),
                            ]);
                    }
                   
            }
            if(isset($params['from_product'])==1){
                    return $this->resultRedirectFactory->create()->setPath('checkout');
            }   
           
            
            return $this->goBack(null, $product);
        }
    } catch (\Magento\Framework\Exception\LocalizedException $e) {
        //print_r($e->getMessage());
        if ($this->_checkoutSession->getUseNotice(true)) {
            $this->messageManager->addNotice(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );
        } else {
            $messages = array_unique(explode("\n", $e->getMessage()));
            foreach ($messages as $message) {
                $this->messageManager->addError(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                );
            }
        }

        $url = $this->_checkoutSession->getRedirectUrl(true);

        if (!$url) {
            $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
            $url = $this->_redirect->getRedirectUrl($cartUrl);
        }

        return $this->goBack($url);

    } catch (\Exception $e) {
        $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
        $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        return $this->goBack();
    }
}
}