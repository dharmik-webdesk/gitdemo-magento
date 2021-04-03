<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Coreoverride\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Convert\ConvertArray;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Profiler;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Category extends \Magento\Catalog\Model\Category
{
    /*protected $flatState;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\CategoryAttributeRepositoryInterface $metadataService,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTreeResource,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $categoryTreeFactory,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Filter\FilterManager $filter,
        Indexer\Category\Flat\State $flatState,
        \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator,
        UrlFinderInterface $urlFinder,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $metadataService,
            $categoryTreeResource,
            $categoryTreeFactory,
            $storeCollectionFactory,
            $url,
            $productCollectionFactory,
            $catalogConfig,
            $filter,
            $flatState,
            $categoryUrlPathGenerator,
            $urlFinder,
            $indexerRegistry,
            $categoryRepository,
            $resource,
            $resourceCollection,
            $data
        );
    } */
     
    protected function _construct()
    {
        // If Flat Index enabled then use it but only on frontend
        if ($this->flatState->isAvailable()) {
            $this->_init('Magento\Catalog\Model\ResourceModel\Category\Flat');
            $this->_useFlatResource = true;
        } else {
            $this->_init('Magento\Catalog\Model\ResourceModel\Category');
        }
    }
    
    public function getImageUrl($attributeCode = 'image')
    {
        $url = false;
        $image = $this->getData($attributeCode);
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    } 
}
