<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Nwdthemes\Revslider\Controller\Adminhtml\Gallery;

use Magento\Framework\App\Filesystem\DirectoryList;
use \Nwdthemes\Revslider\Helper\Data;

class DeleteFiles extends \Nwdthemes\Revslider\Controller\Adminhtml\Gallery
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $coreRegistry);
    }


    /**
     * Delete file from media storage
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                throw new \Exception('Wrong request.');
            }
            $files = $this->getRequest()->getParam('files');

            /** @var $helper \Nwdthemes\Revslider\Helper\Gallery\Images */
            $helper = $this->_objectManager->get('\Nwdthemes\Revslider\Helper\Gallery\Images');
            $path = $this->getStorage()->getSession()->getCurrentPath();
            foreach ($files as $file) {
                $file = $helper->idDecode($file);
                /** @var \Magento\Framework\Filesystem $filesystem */
                $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
                $dir = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $filePath = $path . '/' . $file;
                if ($dir->isFile($dir->getRelativePath($filePath))) {
                    $this->getStorage()->deleteFile($filePath);
                }
            }
            return $this->resultRawFactory->create();
        } catch (\Exception $e) {
            Data::logException($e);
            $result = ['error' => true, 'message' => $e->getMessage()];
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($result);
        }
    }
}
