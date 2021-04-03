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
 * @package     Mageplaza_AffiliatePro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AffiliatePro\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory;

/**
 * Class MassDelete
 * @package Mageplaza\AffiliatePro\Controller\Adminhtml\Campaign
 */
class MassDelete extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    /**
     * @var \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * MassDelete constructor.
     *
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
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
        $delete = 0;
        foreach ($collection as $item) {
            /** @var \Mageplaza\Affiliate\Model\Campaign $item */
            try {
                $item->delete();
                $delete++;
            } catch (\Exception $e) {
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $delete));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
