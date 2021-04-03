<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Controller\Adminhtml\Withdraw;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Affiliate\Model\ResourceModel\Withdraw\CollectionFactory;
use Mageplaza\Affiliate\Model\Withdraw\Status;

/**
 * Class MassApprove
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Withdraw
 */
class MassApprove extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * @var \Mageplaza\Affiliate\Model\ResourceModel\Withdraw\CollectionFactory|\Mageplaza\Affiliate\Model\ResourceModel\Withdraw\WithdrawFactory
     */
    protected $_collectionFactory;

    /**
     * MassApprove constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Context $context
    )
    {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $approve = 0;
        foreach ($collection as $withdraw) {
            /** @var \Mageplaza\Affiliate\Model\Withdraw $withdraw */
            try {
                if ($withdraw->getStatus() != Status::CANCEL) {
                    $withdraw->setData('status', Status::COMPLETE);
                    $withdraw->save();
                    $approve++;
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been approved successfully.', $approve));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
