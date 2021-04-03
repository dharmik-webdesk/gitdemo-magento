<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\FilterBuilder\Controller\Index;
use \Magento\Catalog\Model\Layer\FilterList;
class Index extends \Magento\Framework\App\Action\Action{

    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    protected $jsonResultFactory;

    /**
     * @var Magento\Framework\View\Result\PageFactory
     */
    
    protected $_resultPageFactory;
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Catalog\Model\Layer\Resolver $_layerResolver,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $_imageHelper,
        \Mageplaza\Shopbybrand\Model\ResourceModel\Brand\Collection $_brandCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ReviewFactory $_reviewFactory,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $FilterableAttributeList,
        \Wds\FilterBuilder\Model\AttrimageFactory $wds_AttrimageFactory
    ){

        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->layerResolver = $_layerResolver;//->get();
        $this->objectManager = $objectManager;
        $this->FilterableAttributeList = $FilterableAttributeList;
        $this->_brandCollection = $_brandCollection;
        $this->_imageHelper=$_imageHelper;
        $this->_reviewFactory = $_reviewFactory;
        $this->_wds_AttrimageFactory =$wds_AttrimageFactory;
        $this->filter_list=array(
                                    'brand'=>'Brand',
                                    'configuration'=>'Configurations',
                                    'power'=>'Power',
                                    'rpm'=>'Voltages',
                                    'horsepower'=>'Horsepower',
                                    'cfm'=>'Capacity',
                                    'compability_size'=>'Compatibility Size',
                                );
    }

    public function execute(){
        $response_data['status']=0;
        $response_data['message']='';

        $filterArray=array();
        $current_step=$this->getRequest()->getParam('current_step');
        $next_step=$this->getRequest()->getParam('next_step');
      
        $this->storeManager->setCurrentStore(1);
        $fitler_id=$this->getRequest()->getParam('data_id');
        $store_url = $this->storeManager->getStore()->getBaseUrl();
        $product_list=[];
        $response_data['product_list']['info']=$product_list;
        $response_data['product_list']['total_products']=0;
      
        $filter=$this->getRequest()->getParam('filter');
        $category_id=0;
        if(isset($filter) && isset($filter['category'])){
            if(isset($filter['category']['id']))
                $category_id=$filter['category']['id'];    
        }



        if($current_step=='cfm' && $next_step=='compability_size')
            $next_step='view_product';


        if($next_step=='rpm'){
            $hide_the_power=0;
            $other_selected=0;
            if(isset($filter['power'])){
                if(isset($filter['power']['label'])){
                    $power_label_expldoe=explode(',',$filter['power']['label']);
                    foreach ($power_label_expldoe as $power_label_expldoe_key => $power_label_expldoe_value) {
                        if($power_label_expldoe_value=='Diesel Driven' || $power_label_expldoe_value=='Gas Driven')
                            $hide_the_power=1;
                        else
                            $other_selected=1;
                    }
                    if($other_selected==1)
                        $hide_the_power=0;

                    if($hide_the_power==1)
                         $next_step='horsepower';
                 }

            }
            
            
        }
        

          //$filter      
        
        $response_data['actions']=array(
                                          'current_step'=>$current_step,
                                          'next_step'=>$next_step,
                                          'category_id'=>$category_id,
                                        );

        if($category_id){

            $this->_layerResolver = $this->layerResolver->get();
            $this->_layerResolver->setCurrentCategory($category_id);

            //$this->_layerResolver->addAttributeToFilter();
            unset($filter['category']);
            $final_filter_list=array();
            foreach ($this->filter_list as $key => $value) {
                if(isset($filter[$key]) && $filter[$key]!=''){
                    $filter_to_array=explode(',',$filter[$key]['id']);
                    $final_filter_list[$key]=$filter_to_array;
                }
            }
            

            $product_collection = $this->_layerResolver->getProductCollection()->addAttributeToSelect('image');
            foreach($final_filter_list as $f_key=>$f_data){
                if(count($f_data)<=1){
                    $product_collection = $product_collection->addAttributeToFilter($f_key, array('finset' => $f_data));    
                }else{
                    $tmp_array=array();
                    foreach ($f_data as $s_key => $s_value) {
                        $tmp_array[]=array('finset' => $s_value);
                    }
                    $product_collection = $product_collection->addAttributeToFilter($f_key, $tmp_array);        
                }
            }
            


           
        
        
           
            
            /* */
            
            $filterList = new \Magento\Catalog\Model\Layer\FilterList($this->objectManager,$this->FilterableAttributeList);
            $filterAttributes = $filterList->getFilters($this->_layerResolver);
            
            if($next_step=='view_product' || $next_step=='brand'){
                    $magebrandCollection = $this->_brandCollection->load();
                foreach($magebrandCollection as $brand){
                    $_brand_data[$brand['option_id']]=(string)$brand['image'];
                }  
            }
            $confi_image_data=array();
            if($next_step=='configuration'){
                    $configCollection = $this->_wds_AttrimageFactory->create();
                    $configCollection = $configCollection->getCollection();
                    $configCollection =$configCollection->getData();
                    if(count($configCollection)>0){
                        foreach($configCollection as $config_d){
                            $confi_image_data[$config_d['option_id']]=(string)$config_d['image_url'];
                        }
                    }  
            }
            
            if($next_step=='view_product'){
                $pageSize = 24;

                $currentPage = 1;
                $page=$this->getRequest()->getParam('p');
                if($page)
                   $currentPage = (int)$page;


                $product_collection= $product_collection->setPageSize($pageSize)->setCurPage($currentPage);
                $response_data['product_list']['total_products']=$product_collection->getSize();
                $total_page = ceil($response_data['product_list']['total_products']/$pageSize);
                $response_data['product_list']['total_page']=$total_page;
                $response_data['product_list']['current_page']=$currentPage;
               
                $pager = $this->_resultPageFactory->create()->getLayout()->createBlock(
                    'Magento\Theme\Block\Html\Pager',
                    'fme.news.pager'
                    )->setAvailableLimit(array($pageSize=>$pageSize))->setShowPerPage(true)->setCollection(
                $product_collection
                )->setTemplate("Wds_FilterBuilder::pager.phtml");
                //);


                $response_data['product_list']['pagination_data']=$pagination_data=$pager->toHtml();
                
                
        
                $abstractProductBlock =$this->_resultPageFactory->create()->getLayout()->createBlock('\Magento\Catalog\Block\Product\AbstractProduct');

                foreach($product_collection as $product){
                        $stock_label=null;
                        if($product->getStockAvailabilityStatus()){
                            $stock_label=$product->getAttributeText('stock_availability_status');
                            if(is_array($stock_label))
                                $stock_label=implode($stock_label,', ');
                        }
                        if(empty($stock_label))
                            $stock_label='In stock';  
                        
                                            


                    $info = array();
                   
$name=  strip_tags( $product->getName());
$name= (strlen($name)>45)?substr($name,0,45)."...":$name;

 $block = trim($this->_resultPageFactory->create()->getLayout()
                                    ->createBlock("Magento\Catalog\Block\Product\ListProduct")
                                    ->setData('_product', $product)
                                    ->setTemplate("Magento_Catalog::product/produt_tags.phtml")->toHtml());
$brand_id = $product->getBrand();
$rating_count= $this->getRatingSummary($product);
$brand_logs='';
if(array_key_exists($brand_id, $_brand_data)):
    if(! empty(trim($_brand_data[$brand_id]))):
        $brand_logs = '<img src="'.$store_url.'pub/media/'.$_brand_data[$brand_id].'" />';
    endif;
endif;

$reviwe_html='';
if($rating_count['ratingSummary']>0){
    $reviwe_html='<div class="product-reviews-summary short">
        <div class="rating-summary">
        <div class="rating-result" title="'.$rating_count['ratingSummary'].'%">
            <span style="width:'.$rating_count['ratingSummary'].'%"><span>'.$rating_count['ratingSummary'].'%</span></span>
        </div>
    </div>
        <div class="reviews-actions">
        <a class="action view" href="'.$product->getProductUrl().'#reviews">'.$rating_count['reviewCount'].' &nbsp;<span>Review</span></a>
    </div>
</div>';
}

$info='<li class="item product product-item">
<div class="product-item-info"><a class="product photo product-item-photo" href="'.$product->getProductUrl().'" title="'.$product->getName().'"><img src="'.$this->_imageHelper->init($product, 'small_image', ['type'=>'small_image'])->keepAspectRatio(true)->resize('150','150')->getUrl().'" alt="'.$product->getName().'"/></a>
'.$reviwe_html.''.$block.'<div class="model"><strong>Model:</strong> '.$product->getSku().'</div>
<div class="brand_img_mn">'.$brand_logs.'</div>
<div class="product details product-item-details loaded_factory_model">
<strong class="product name product-item-name">
    <a class="product-item-link" href="'.$product->getProductUrl().'" title="'.$product->getName().'">'.$name.'</a>
</strong>
<div class="product-price discound_price_box">
    <div class="custom_pricing">
'.$abstractProductBlock->getProductPrice($product).'
    </div>
</div>
    <div class="product-item-inner">
        <div class="product actions product-item-actionss">
            <div class="actions-primary">
                <p class="availability in-stock"><span>'.$stock_label.'</span></p>
                <a href="'.$product->getProductUrl().'" title="'.$product->getName().'" class="btn green">View Product</a>
            </div>    
        </div>
    </div>
</div>
</div>
</li>';

 $product_list[] = $info;
                }

            }else{
                $i = 0;
                if(count($filterAttributes)){
                    foreach($filterAttributes as $filter){

                        $filter_code = $filter->getRequestVar(); //Gives the request param name such as 'cat' for Category, 'price' for Price
                        $availablefilter = $filter_code; //Gives Display Name of the filter such as Category,Price etc.
                        if($next_step==$availablefilter || ($next_step=='cfm' && $availablefilter=='compability_size')){

                            $items = $filter->getItems(); //Gives all available filter options in that particular filter
                            $filterValues = array('name'=>(string)$filter->getName());
                            $j = 0;
                           if(count($items)){
                              foreach($items as $item){
                                $filterValues['items'][$j]['label'] = strip_tags($item->getLabel());
                                $filterValues['items'][$j]['value'] = $item->getValue();

                                if($next_step=='brand'){
                                    if(isset($_brand_data[$item->getValue()]))
                                        if(! empty($_brand_data[$item->getValue()])){
                                        $filterValues['items'][$j]['image'] =  $store_url.'pub/media/'.$_brand_data[$item->getValue()];
                                        }else{
                                            $filterValues['items'][$j]['image'] =  '';    
                                        }
                                    else
                                        $filterValues['items'][$j]['image'] =  '';
                                }else if($next_step=='configuration'){
                                    if(isset($confi_image_data[$item->getValue()]))
                                        if(! empty($confi_image_data[$item->getValue()])){
                                        $filterValues['items'][$j]['image'] =  $store_url.'pub/media/'.$confi_image_data[$item->getValue()];
                                        }else{
                                            $filterValues['items'][$j]['image'] =  '';    
                                        }
                                    else
                                        $filterValues['items'][$j]['image'] =  '';
                                }
                                
                                // $filterValues['items'][$j]['count'] = $item->getCount(); //Gives no. of products in each filter options
                                $j++;
                              }
                              if(!empty($filterValues['items'])){
                                 $filterArray['availablefilter'][$availablefilter] =  $filterValues;
                              }
                           }
                           $i++;
                        }
                     }
                }
            } 

            if($next_step=='cfm'){
                //unset($filterArray['availablefilter']['cfm']);
                if(isset($filterArray['availablefilter']['cfm']) && 
                    count($filterArray['availablefilter']['cfm'])>0){
                }else{
                    if(isset($filterArray['availablefilter']['compability_size']) && 
                    count($filterArray['availablefilter']['compability_size'])>0){
                             $response_data['actions']['next_step']='compability_size';
                    }
                }

            }

            /** @var \Magento\Framework\Controller\Result\Json $result */
            /** You may introduce your own constants for this custom REST API */
            //$result->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_SUCCESS);
            //$data['current_step']=
            //$data['next_step']=
            $response_data['status']=1;
            $response_data['data']=$filterArray;
            $response_data['product_list']['info']=$product_list;

        }else{
                $response_data['message']='Please choose category';
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($response_data);
        return $result;
    }
    public function getRatingSummary($product){ 
        $this->_reviewFactory->create()->getEntitySummary($product, $this->storeManager->getStore()->getId());
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        $reviewCount   = $product->getRatingSummary()->getReviewsCount();
        $response=array();
        $response['ratingSummary'] =$ratingSummary;
        $response['reviewCount'] = $reviewCount;
        return $response;
    }
}