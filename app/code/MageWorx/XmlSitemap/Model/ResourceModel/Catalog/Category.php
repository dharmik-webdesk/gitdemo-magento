<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\XmlSitemap\Model\ResourceModel\Catalog;

use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
/**
 * {@inheritdoc}
 */
class Category extends \Magento\Sitemap\Model\ResourceModel\Catalog\Category
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $query;

    /**
     * @var bool
     */
    protected $readed = false;

    /**
     * @var \MageWorx\XmlSitemap\Helper\Data
     */
    protected $helperSitemap;

    /**
     * Get category collection array
     * Call this function while !isCollectionReaded() to read all collection
     *
     * @param null|string|bool|int|Store $storeId
     * @return array|bool
     */
    public function getLimitedCollection($storeId, $limit)
    {
        $categories = [];

        /* @var $store Store */
        $store = $this->_storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        if ($limit <= 0) {
            return false;
        }

        if (!isset($this->query)) {
            $connect = $this->getConnection();
            $this->_select = $connect->select()->from(
                $this->getMainTable()
            )->where(
                $this->getIdFieldName() . '=?',
                $store->getRootCategoryId()
            );
            $categoryRow = $connect->fetchRow($this->_select);

            if (!$categoryRow) {
                return false;
            }

            $this->_select = $connect->select()->from(
                ['e' => $this->getMainTable()],
                [$this->getIdFieldName(), 'updated_at']
            )->joinLeft(
                ['url_rewrite' => $this->getTable('url_rewrite')],
                'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
                . $connect->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
                . $connect->quoteInto(' AND url_rewrite.entity_type = ?', CategoryUrlRewriteGenerator::ENTITY_TYPE),
                ['url' => 'request_path']
            )->where(
                'e.path LIKE ?',
                $categoryRow['path'] . '/%'
            );

            $this->_addFilter($storeId, 'is_active', 1);
            $this->_addFilter($storeId, 'in_xml_sitemap', 1);


            $this->query = $connect->query($this->_select);
            $this->readed = true;
        }

        for ($i = 0; $i < $limit; $i++) {
            if (!$row =  $this->query->fetch()) {
                $this->readed = true;
                break;
            }

            $category = $this->_prepareCategory($row);
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * @return bool
     */
    public function isCollectionReaded() {
        return $this->readed;
    }
}
