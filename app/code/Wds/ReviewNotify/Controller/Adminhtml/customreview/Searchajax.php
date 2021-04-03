<?php
namespace Wds\ReviewNotify\Controller\Adminhtml\customreview;

use Magento\Backend\App\Action;

class Searchajax extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
	protected $_jsonEncoder;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\Controller\Result\JsonFactory $resultPageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
		$this->_jsonEncoder = $encoder;
		$this->_productCollectionFactory = $productCollectionFactory;        
		$this->catalogProductVisibility = $catalogProductVisibility;
    }

    public function execute()
    {

    	$res['status']='0';
    	$res['data']=array();
	       if($this->getRequest()->isAjax()){
        	$search=$this->getRequest()->get('search');
        	if($search){
        		$page_size=10;
				$product_collection = $this->_productCollectionFactory->create()
					//->setStoreId($store_id)
//					->addStoreFilter()
//					->addFieldToFilter('entity_id', array('in' => $product_list_array))
					->addAttributeToFilter(array(
                            array('attribute' => 'sku', 'like' => '%'.$search.'%')
                           // array('attribute' => 'name', 'like' => '%'.$search.'%')
                        ))
					->addAttributeToFilter('status', 1)
					->addAttributeToSelect(array('name'))
					->addAttributeToSelect('status')
					->setOrder('created_at', 'desc')
					->setPageSize($page_size);
				//echo $product_collection->getSelect();	

				if($product_collection->count()>0){
					$res['status']='1';
					foreach ($product_collection as $value) {
						$res['data'][]=array('key'=>$value->getId(),'name' =>$value->getName() );
					}
    			}
			}
        }
		$this->getResponse()->representJson($this->_jsonEncoder->encode($res));
		return false;
    }
}
