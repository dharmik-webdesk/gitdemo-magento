<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigation
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Plugin\Model\Adapter;

/**
 * Class Preprocessor
 * @package Mageplaza\LayeredNavigation\Model\Plugin\Adapter
 */
class CatalogView
{
    /**
     * @var \Magento\Framework\App\RequestInterface 
     */
	protected $_request;


    /**
     * CatalogView constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request
	)
	{
		$this->_request      = $request;
	}


	public function afterIsApplicable(\Magento\CatalogSearch\Model\Adapter\Aggregation\Checker\Query\CatalogView $subject, $result)
	{
		
		if($this->_request->getFullActionName()=='mpbrand_index_view'){
			return true;
		}

		return $result;
	}
}
