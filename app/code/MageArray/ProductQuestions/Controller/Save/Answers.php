<?php

namespace MageArray\ProductQuestions\Controller\Save;

use Magento\Framework\Controller\ResultFactory;

class Answers extends \Magento\Framework\App\Action\Action
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
        $post = $this->getRequest()->getPostValue();
        $users = $this->customerSession->getCustomer()->getId();
        if ($post) {
            $currenttime = time();
            if ($users) {
                $post['customer_id'] = $users;
            }

            $question = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Questions')
                ->load($post['product_questions_id']);
            $storeId = $question->getStoreId();
            $model = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Answers');

            $model->setData($post);
            $scopeConfig = $this->_objectManager
                ->create('Magento\Framework\App\Config\ScopeConfigInterface');

            $configPath = 'productquestions/general/approve_ans';
            $value = $scopeConfig->getValue(
                $configPath,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $configEmail = 'productquestions/general/emailonanswer';
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
                    ->load($question->getProductId());
                $data['name'] = $post['author_name'];
                $data['email'] = $post['author_email'];
                $data['question'] = $question->getQuestions();
                $data['answers'] = $post['answers'];
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

                //$receiverEmail = [$adminEmail, $question->getAuthorEmail()];
                //$receiverName = [$adminName, $question->getAuthorName()];

                $receiverEmail = [$adminEmail];
                $receiverName = [$adminName];

                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier(
                        'productquestions_general_ans_email_template'
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

            $this->messageManager
                ->addSuccess(__('Your answer has been submitted successfully.'));

        }
        
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
}