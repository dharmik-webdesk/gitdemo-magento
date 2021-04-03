<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Technicaldata\Block\Catalog\Product\Edit;

//use Magento\Backend\Block\Widget\Tabs as WigetTabs;
class Tabs
{
    
    public function __construct()
    {
        //parent::__construct();
    }

    protected function _prepareLayout()
    {
		parent::_prepareLayout();
		if($this->getProduct()->getTypeID()!= \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE)
		{
            $product = $this->getProduct();
	        if (!($setId = $product->getAttributeSetId())) {
	            $setId = $this->getRequest()->getParam('set', null);
	        }
	        if ($setId) {
             	$this->addTab(
                    'technicaldata',
                    [
                        'label' => __('Technical Data'),
                        'content' => $this->_translateHtml(
                            $this->getLayout()->createBlock(
                                'Wds\Technicaldata\Block\Catalog\Product\Edit\Tab\Technicaldata'
                            )->toHtml()
                        ),
                        
                    ]
                );
			}
		}
	}
}
