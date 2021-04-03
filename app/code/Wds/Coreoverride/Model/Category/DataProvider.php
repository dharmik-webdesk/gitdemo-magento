<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Coreoverride\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Category\Attribute\Backend\Image as ImageBackendModel;
/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
   
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        EavValidationRules $eavValidationRules,
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        Config $eavConfig,
        \Magento\Framework\App\RequestInterface $request,
        CategoryFactory $categoryFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $eavValidationRules,
            $categoryCollectionFactory,
            $storeManager,
            $registry,
            $eavConfig,
            $request,
            $categoryFactory,
            $meta,
            $data
        );
       
    }


    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $category = $this->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();
            $categoryData = $this->addUseDefaultSettings($category, $categoryData);
            $categoryData = $this->addUseConfigSettings($categoryData);
            $categoryData = $this->filterFields($categoryData);
            $categoryData = $this->convertValues($category, $categoryData);
            $this->loadedData[$category->getId()] = $categoryData;
        }
        return $this->loadedData;
    }

    protected function convertValues($category, $categoryData)
    {
        foreach ($category->getAttributes() as $attributeCode => $attribute) {
            if (!isset($categoryData[$attributeCode])) {
                continue;
            }
			
            if ($attribute->getBackend() instanceof ImageBackendModel) {
					unset($categoryData[$attributeCode]);
					$categoryData[$attributeCode][0]['name'] = $category->getData($attributeCode);
					$categoryData[$attributeCode][0]['url'] = $category->getImageUrl($attributeCode);	  
			}			
        }
        return $categoryData;
    }
}
