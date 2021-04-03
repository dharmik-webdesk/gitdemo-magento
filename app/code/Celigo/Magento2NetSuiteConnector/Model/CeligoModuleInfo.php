<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Celigo\Magento2NetSuiteConnector\Model;

use \Celigo\Magento2NetSuiteConnector\Logger\Logger;
use \Magento\Framework\Module\ResourceInterface;
use \Magento\Framework\App\ProductMetadataInterface;

/**
 * Celigo sales order repository object.
 */
class CeligoModuleInfo implements \Celigo\Magento2NetSuiteConnector\Api\CeligoModuleInfoInterface
{

    private $logger;

    private $moduleResource;

    private $productMetadata;
   
    public function __construct(
        Logger $logger,
        ResourceInterface $moduleResource,
        ProductMetadataInterface $productMetadata
    ) {
        $this->logger = $logger;
        $this->moduleResource = $moduleResource;
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return json_encode([
            'moduleVersion' =>  $this->moduleResource->getDbVersion('Celigo_Magento2NetSuiteConnector'),
            'magentoVersion' =>  $this->productMetadata->getVersion(),
            'magentoEdition' => $this->productMetadata->getEdition()
        ]);
    }
}
