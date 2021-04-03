<?php

namespace MageArray\ProductQuestions\Controller\Adminhtml\Questions;

class Save extends \MageArray\ProductQuestions\Controller\Adminhtml\Questions
{
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->_objectManager
                ->create('MageArray\ProductQuestions\Model\Questions');
            if (array_key_exists("created_at", $data)) {
                $date = date_create($data['created_at']);
                $data['created_at'] = date_format($date, 'Y-m-d H:i:s');
            }

            $store = implode(",", $data['stores']);
            if (array_key_exists("customer_id", $data)) {
                $customer = $this->_objectManager
                    ->create('Magento\Customer\Model\Customer')
                    ->load($data['customer_id']);
                $firstName = $customer->getFirstname();
                $lastName = $customer->getLastname();
                $data['author_name'] = $firstName . ' ' . $lastName;
                $data['author_email'] = $customer->getEmail();
                $data['asked_from'] = 'Customer';
            }

            if (array_key_exists("products_id", $data)) {
                $data['product_id'] = $data['products_id'];
            }

            $id = $this->getRequest()->getParam('product_questions_id');
            if (empty($id)) {
                if (!array_key_exists("products_id", $data)) {
                    $this->messageManager->addError('Please select product.');
                    $this->_redirect('*/*/new');
                    return;
                } else {
                    if (empty($data['products_id'])) {
                        $this->messageManager
                            ->addError('Please select product.');
                        $this->_redirect('*/*/new');
                        return;
                    }
                }
            } else {
                if (array_key_exists("products_id", $data)) {
                    if (empty($data['products_id'])) {
                        $this->messageManager->addError('Please select product.');
                        $this->_redirect(
                            '*/*/edit',
                            [
                                'product_questions_id' => $id,
                                '_current' => true
                            ]
                        );
                        return;
                    }
                }
            }
            
            if ($id) {
                $ansModel = $this->_objectManager
                    ->create('MageArray\ProductQuestions\Model\Answers')
                    ->getCollection();
                $ansModel->addFieldToFilter('product_questions_id', $id);
                foreach ($ansModel as $ans) {
                    $ansCollection = $this->_objectManager
                        ->create('MageArray\ProductQuestions\Model\Answers')
                        ->load($ans->getAnswersId());
                    $ansCollection->setData('store_id', $store);
                    $ansCollection->save();
                }

                $model->load($id);
            }

            try {
                $model->setData($data);
                $model->setData('store_id', $store);
                $model->save();
                $this->messageManager
                    ->addSuccess(__('Question has been saved.'));
                $this->_objectManager
                    ->get('Magento\Backend\Model\Session')
                    ->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            'product_questions_id' => $model->getId(),
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
                        __('Something went wrong while saving question.')
                    );
            }

            $this->_getSession()->setFormData($data);
            $qid = $this->getRequest()->getParam('product_questions_id');
            $this->_redirect(
                '*/*/edit',
                ['product_questions_id' => $qid]
            );
            return;
        }

        $this->_redirect('*/*/');

    }
}
