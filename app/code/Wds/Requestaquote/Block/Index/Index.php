<?php

namespace Wds\Requestaquote\Block\Index;


class Index extends \Magento\Framework\View\Element\Template {

	protected $_regionFactory;

    public function __construct(
    		\Magento\Catalog\Block\Product\Context $context, 
			\Magento\Directory\Model\RegionFactory	$_regionFactory,
			array $data = []
    ){
    	$this->_regionFactory = $_regionFactory;
        parent::__construct($context, $data);

    }


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getCountryRegions()
    {
        $regions = $this->_regionFactory->create()->getCollection()->addFieldToFilter('country_id','US');
		return $regionCollection = $regions->getData();
    }

}