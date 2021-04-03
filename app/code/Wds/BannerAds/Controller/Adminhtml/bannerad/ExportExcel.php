<?php

namespace Wds\BannerAds\Controller\Adminhtml\bannerad;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class ExportExcel extends \Magento\Backend\App\Action
{
    protected $_fileFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $_fileFactory
    ) {
        $this->_fileFactory = $_fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout(false);

        $fileName = 'bannerad.xml';

        $exportBlock = $this->_view->getLayout()->createBlock('Wds\BannerAds\Block\Adminhtml\Bannerad\Grid');

        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile(),
            DirectoryList::VAR_DIR
        );
    }
}