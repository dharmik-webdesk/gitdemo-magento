<?php
/**
 * Catalog product copier. Creates product duplicate
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Technicaldata\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;

class Copier extends \Magento\Catalog\Model\Product\Copier
{
    

    public function copy(\Magento\Catalog\Model\Product $product)
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();

        /** @var \Magento\Catalog\Model\Product $duplicate */
        $duplicate = $this->productFactory->create();
        $duplicate->setData($product->getData());
        $duplicate->setOptions([]);
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalId($product->getEntityId());
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

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                    $connection = $resource->getConnection();
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
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $product->getResource()->duplicate(
            $product->getData($metadata->getLinkField()),
            $duplicate->getData($metadata->getLinkField())
        );
        return $duplicate;
    }

    private function getOptionRepository()
    {
        if (null === $this->optionRepository) {
            $this->optionRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Model\Product\Option\Repository');
        }
        return $this->optionRepository;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     * @deprecated
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }
   
}
