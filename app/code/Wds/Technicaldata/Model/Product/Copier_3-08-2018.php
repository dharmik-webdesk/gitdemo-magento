<?php
/**
 * Catalog product copier. Creates product duplicate
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Technicaldata\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

class Copier extends \Magento\Catalog\Model\Product\Copier
{
    
    protected $_resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $_resourceConnection
    ) {
        $this->_resourceConnection = $_resourceConnection;
    }
    /**
     * Create product duplicate
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function copy(\Magento\Catalog\Model\Product $product)
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();

        /** @var \Magento\Framework\EntityManager\EntityMetadataInterface $metadata */
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);

        /** @var \Magento\Catalog\Model\Product $duplicate */
        $duplicate = $this->productFactory->create();
        $productData = $product->getData();
        $productData = $this->removeStockItem($productData);
        $duplicate->setData($productData);
        $duplicate->setOptions([]);
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalLinkId($product->getData($metadata->getLinkField()));
        $duplicate->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        $duplicate->setCreatedAt(null);
        $duplicate->setUpdatedAt(null);
        $duplicate->setId(null);
        $duplicate->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->copyConstructor->build($product, $duplicate);
        $isDuplicateSaved = false;
        do {
            $urlKey = $duplicate->getUrlKey();
            $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
                ? $matches[1] . '-' . ($matches[2] + 1)
                : $urlKey . '-1';
            $duplicate->setUrlKey($urlKey);
            try {
                $duplicate->save();
                /* --- KM Customize for duplicate technical data   -- */
                $dupid = $duplicate->getId();
               
                if($dupid){
                    $connection = $this->_resourceConnection->getConnection();
                    $tableName = $resource->getTableName('technicaldata');

                    $product_id = $product->getId();
                    $sql = "Select * FROM " . $tableName." where product_id =".$product_id;
                    $result = $connection->fetchAll($sql);

                    if(count($result)>0){
                         $sqlins = "INSERT INTO " . $tableName." (product_id, attribute, attributevalue, attributegroup) values "; 
                              foreach ($result as $res) {
                                   $productId = $dupid;
                                   $attributename = addslashes($res['attribute']);
                                   $attributename_val = addslashes($res['attributevalue']);
                                   $attributename_grp = $res['attributegroup'];
                                   $valuesArr[] = "('$dupid', '$attributename', '$attributename_val', '$attributename_grp')";

                              }
                              $sqlins .= implode(',', $valuesArr);
                              $connection->query($sqlins);
                    }
                }
                 /* --- KM Customize for duplicate technical data   -- */
                $isDuplicateSaved = true;
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            }
        } while (!$isDuplicateSaved);
        $this->getOptionRepository()->duplicate($product, $duplicate);
        $product->getResource()->duplicate(
            $product->getData($metadata->getLinkField()),
            $duplicate->getData($metadata->getLinkField())
        );
        return $duplicate;
    }

    /**
     * @return Option\Repository
     * @deprecated 101.0.0
     */
    private function getOptionRepository()
    {
        if (null === $this->optionRepository) {
            $this->optionRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Catalog\Model\Product\Option\Repository::class);
        }
        return $this->optionRepository;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     * @deprecated 101.0.0
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\MetadataPool::class);
        }
        return $this->metadataPool;
    }

    /**
     * Remove stock item
     *
     * @param array $productData
     * @return array
     */
    private function removeStockItem(array $productData)
    {
        if (isset($productData[ProductInterface::EXTENSION_ATTRIBUTES_KEY])) {
            $extensionAttributes = $productData[ProductInterface::EXTENSION_ATTRIBUTES_KEY];
            if (null !== $extensionAttributes->getStockItem()) {
                $extensionAttributes->setData('stock_item', null);
            }
        }
        return $productData;
    }
}
