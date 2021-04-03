<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog category image attribute backend model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Wds\Coreoverride\Model\Category\Attribute\Backend;

class Image extends \Magento\Catalog\Model\Category\Attribute\Backend\Image
{
	const ADDITIONAL_DATA_PREFIX = '_additional_data_';
   
    private $objectManager;
	
	private $imageUploader;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
		$this->objectManager = $objectManager;
       parent::__construct(
		$logger,
		$filesystem,
		$fileUploaderFactory
		);
	}

    /**
     * @param array $value Attribute value
     * @return string
     */
    protected function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
	}

    /**
      * Avoiding saving potential upload data to DB
      *
      * @param \Magento\Framework\DataObject $object
      * @return $this
     */

    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $value = $object->getData($attributeName);
		if ($imageName = $this->getUploadedImageName($value)) {
            $object->setData(self::ADDITIONAL_DATA_PREFIX . $attributeName, $value);
            $object->setData($attributeName, $imageName);
        } else if (!is_string($value)) {
            $object->setData($attributeName, '');
        }

        return parent::beforeSave($object);
    }

    /**

     *
     * @return \Magento\Catalog\Model\ImageUploader
     *
     * @deprecated
     */
    private function getImageUploader()
    {
        if ($this->imageUploader === null) {
            $this->imageUploader = $this->objectManager->get(\Magento\Catalog\CategoryImageUpload::class);
        }

        return $this->imageUploader;
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData(self::ADDITIONAL_DATA_PREFIX . $this->getAttribute()->getName());
		
        if ($imageName = $this->getUploadedImageName($value)) {
			
            try {
               $this->getImageUploader()->moveFileFromTmp($imageName);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        return $this;
    }
}
