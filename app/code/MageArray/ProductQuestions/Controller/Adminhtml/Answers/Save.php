<?php

namespace MageArray\ProductQuestions\Controller\Adminhtml\Answers;

class Save extends \MageArray\ProductQuestions\Controller\Adminhtml\Answers
{

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $model = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Answers');
            $question = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Questions')
                ->load($data['product_questions_id']);
            $storeId = $question->getStoreId();
            $data['store_id'] = $storeId;
            if (array_key_exists("created_at", $data)) {
                $date = date_create($data['created_at']);
                $data['created_at'] = date_format($date, 'Y-m-d H:i:s');
            }
            
            if ($data['answers_from'] == 'Admin') {
                $data['author_name'] = $data['admin_name'];
                $data['author_email'] = $data['admin_email'];
            } elseif ($data['answers_from'] == 'Guest') {
                $data['author_name'] = $data['guest_name'];
                $data['author_email'] = $data['guest_email'];
            } else {
                $customer = $this->_objectManager
                    ->create('Magento\Customer\Model\Customer')
                    ->load($data['customer_id']);
                $firstName = $customer->getFirstname();
                $lastName = $customer->getLastname();
                $data['author_name'] = $firstName . ' ' . $lastName;
                $data['author_email'] = $customer->getEmail();

            }

            $id = $this->getRequest()->getParam('answers_id');
            if ($id) {
                $model->load($id);
            }

            try {

                $model->setData($data);
                $model->save();
                if ($id == '') {
                    $scopeConfig = $this->_objectManager
                        ->create(
                            'Magento\Framework\App\Config\ScopeConfigInterface'
                        );
                    $configPath = 'productquestions/general/emailonanswer';
                    $sendEmail = $scopeConfig->getValue(
                        $configPath,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $configAdminName = 'productquestions/general/admin_name';
                    $adminName = $scopeConfig->getValue(
                        $configAdminName,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $configAdminEmail = 'productquestions/general/admin_email';
                    $adminEmail = $scopeConfig->getValue(
                        $configAdminEmail,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                    if ($sendEmail == 1) {
                        $productInfo = $this->_objectManager
                            ->create('Magento\Catalog\Model\Product')
                            ->load($question->getProductId());
                        $post['name'] = $data['author_name'];
                        $post['email'] = $data['author_email'];
                        $post['question'] = $question->getQuestions();
                        $post['answers'] = $data['answers'];
                        $post['product'] = $productInfo->getName();
                        $post['product_url'] = $productInfo->getProductUrl();
                        
                        if ($data['status'] == 1) {
                            $post['status'] = 'Pending';
                        }
                        
                        if ($data['status'] == 2) {
                            $post['status'] = 'Approved';
                        }
                        
                        $this->inlineTranslation->suspend();
                        $postObject = new \Magento\Framework\DataObject();
                        $postObject->setData($post);
                        $sender = [
                            'name' => $this->_escaper->escapeHtml($adminName),
                            'email' => $this->_escaper->escapeHtml($adminEmail),
                        ];
                        $authorEmail = $question->getAuthorEmail();
                        $authorName = $question->getAuthorName();
                        $transport = $this->_transportBuilder
                            ->setTemplateIdentifier('productquestions_general_ans_email_template')
                            ->setTemplateOptions(
                                [
                                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                ]
                            )
                            ->setTemplateVars(['data' => $postObject])
                            ->setFrom($sender)
                            ->addTo($authorEmail, $authorName)
                            ->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    }

                }

                $this->messageManager->addSuccess(__('Answer has been saved.'));
                $this->_objectManager
                    ->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            'answers_id' => $model->getId(),
                            '_current' => true
                        ]
                    );
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager
                    ->addException(
                        $e,
                        __('Something went wrong while saving answer.')
                    );

            }

            $this->_getSession()->setFormData($data);
            $ansId = $this->getRequest()->getParam('answers_id');
            $this->_redirect(
                '*/*/edit',
                ['answers_id' => $ansId]
            );
            return;
        }

        $this->_redirect('*/*/');

    }
}
