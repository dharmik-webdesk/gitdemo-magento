<?php
namespace MageArray\ProductQuestions\Controller\Save;

use Magento\Framework\Controller\ResultFactory;

class Questions extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->customerSession = $customerSession;
        $this->_escaper = $escaper;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_objectManager = $context->getObjectManager();
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);
        // MParmar custom validation
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $g_recaptcha_response = $this->getRequest()->getPost('g-recaptcha-response');
        $helper_Formvalidater = $objectManager->get('Wds\Coreoverride\Helper\Formvalidater');

        
        $check_rquest_url = $helper_Formvalidater->varify_request_url();


        $google_captcha_validator = $helper_Formvalidater->verify_google_captcha($g_recaptcha_response);
        if($google_captcha_validator == false){
            $this->messageManager->addErrorMessage("Captcha is not valid, Please try again.");
            header("Location: " . $_SERVER["HTTP_REFERER"]);exit;
        } 



        $post = $this->getRequest()->getPostValue();
        $users = $this->customerSession->getCustomer()->getId();
        if ($post) {
            $currenttime = date('Y-m-d H:i:s');
            if (array_key_exists('visibility', $post)) {
                $post['visibility'] = "Private";
            } else {
                $post['visibility'] = "Public";
            }
            
            if ($users) {
                $post['customer_id'] = $users;
            }
            
            $model = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Questions');
            $model->setData($post);
            $scopeConfig = $this->_objectManager
                ->create('Magento\Framework\App\Config\ScopeConfigInterface');
            $configPath = "productquestions/general/approve_que";
            $value = $scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $configEmail = 'productquestions/general/emailonquestion';
            $sendEmail = $scopeConfig->getValue(
                $configEmail,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $configName = 'productquestions/general/admin_name';
            $adminName = $scopeConfig->getValue(
                $configName,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $configEmail = 'productquestions/general/admin_email';
            $adminEmail = $scopeConfig->getValue(
                $configEmail,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $storeConfig = $this->_objectManager
                ->create('\Magento\Store\Model\StoreManagerInterface');
            $store = $storeConfig->getStore();
            $storeId = $store->getData('store_id');
            if ($value == 0) {
                $model->setData('status', 1);
                $status = "Pending";
            } else {
                $model->setData('status', 2);
                $status = "Approved";
            }
            
            $model->setData('store_id', $storeId);
            $model->setData('created_at', $currenttime);
            $model->save();
            if ($sendEmail == 1) {
                $productInfo = $this->_objectManager
                    ->create('Magento\Catalog\Model\Product')
                    ->load($post['product_id']);
                $data['name'] = $post['author_name'];
                $data['email'] = $post['author_email'];
                $data['question'] = $post['questions'];
                $data['product'] = $productInfo->getName();
                $data['product_url'] = $productInfo->getProductUrl();
                $data['status'] = $status;
                $this->inlineTranslation->suspend();
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);
                $sender = [
                    'name' => $this->_escaper->escapeHtml($adminName),
                    'email' => $this->_escaper->escapeHtml($adminEmail),
                ];

                // Only admin get email //
               // $receiverEmail = [$adminEmail, $data['email']];
               // $receiverName = [$adminName, $data['name']];
                $receiverEmail = [$adminEmail];
                $receiverName = [$adminName];

                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier(
                        'productquestions_general_que_email_template'
                    )
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($sender)
                    ->addTo($receiverEmail, $receiverName)
                    ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            }
            
            $this->messageManager->addSuccess(
                __('Your question has been received and Notification will be send when answer is published')
            );
        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}