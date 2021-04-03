<?php
namespace MageArray\ProductQuestions\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getStoreConfig($storePath)
    {
        $storeConfig = $this->_scopeConfig->getValue(
            $storePath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeConfig;
    }
}
