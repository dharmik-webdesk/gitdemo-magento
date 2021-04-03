<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Coreoverride\Block\Html;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;

/**
 * Html page top menu block
 */
class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{

    protected $product_collection;
    protected $_categoryFactory;
    protected $_produdctCollectionFactory;
    protected $_imageHelper;
    
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        array $data = [],
        \Magento\Framework\Filesystem $filesystem,         
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Catalog\Model\CategoryFactory $_categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $_produdctCollectionFactory,
        \Magento\Catalog\Helper\Image $_imageHelper
    ) {
        $this->_filesystem = $filesystem;               
        $this->_imageFactory = $imageFactory;
        $this->_categoryFactory = $_categoryFactory;
        $this->_produdctCollectionFactory = $_produdctCollectionFactory;
        $this->_imageHelper = $_imageHelper;
        parent::__construct(
            $context,
            $nodeFactory,
            $treeFactory,
            $data
        );
    }
   

      protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            $menu_label = $child->getDataByKey('menu_label');
            if(!empty($menu_label)) $html .= '<span class="megamenu-label'." label".$counter.'">' .$menu_label. '</span>';
            
          

            if($childLevel==0){
            
                $child_menu_html='';
                if($child['cat_id']==684){
                    $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $child_menu_html= '<div class="custom_menu_parent">'.$this->_objectManager
                        ->create('Magento\Cms\Block\Block')
                        ->setBlockId('oilless_mega_menu')
                        ->toHtml().'</div>';

                }
                if(isset($child['cat_id'])){
                      $product_list=$this->getProductList($child['cat_id']);  
                }
                $custom_class=($product_list)?'has_left_products':'';  

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            
                
            if($child['cat_id']==684 && $child_menu_html!=''){


                $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName())
                . '</span></a><div class="custom_sub_menu '.$custom_class.' custom_menu_top_level"><div class="custom_sub_menu_wi">'.$child_menu_html.$product_list.$child['total_product_list'].$child['get_select'].'</div></div></li>';


            }else{
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName())
                . '</span></a><div class="custom_sub_menu '.$custom_class.'"><div class="custom_sub_menu_wi"><h3 class="sub_menu_title">'.$this->escapeHtml($child->getName()).'<span class="custom_view_morelink"> <em>></em> <a href="' . $child->getUrl() . '" ' . $outermostClassCode . ' > View All</a></span></h3> '.$this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) .$product_list.$child['total_product_list'].$child['get_select'].'</div></div></li>';
             }}else{
                $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
                    $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName())
                . '</span></a>'.$this->_addSubMenu(
                    $child,
                    $childLevel,
                    $childrenWrapClass,
                    $limit
                ) . '</li>';
            

             }   
            $itemPosition++;
            $counter++;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    public function resize($image, $width = null, $height = null)
    {
        $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('catalog/category/').$image;

        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/v3/'.$width.'/').$image;         
    if(file_exists($absolutePath)){
        return false;
    }
    if(!file_exists($imageResized)){

        //create image factory...
        $imageResize = $this->_imageFactory->create();         
        $imageResize->open($absolutePath);
    $imageResize->constrainOnly(FALSE);         
        $imageResize->keepTransparency(TRUE);         
        $imageResize->keepFrame(FALSE);         
        $imageResize->keepAspectRatio(TRUE);         
        $imageResize->resize($width,$height);  
   
        //destination folder                
        $destination = $imageResized ;    
        //save image      
        $imageResize->save($destination);         
        //imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 127));
    }        
    $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/v3/'.$width.'/'.$image;
        return $resizedURL;
  }

  function getProductList($cat_id){
        

       
        
        $categoryId= $cat_id;
        $category=$this->_categoryFactory->create()->load($categoryId);
        $abstractProductBlock=$this->getLayout()->createBlock('\Magento\Catalog\Block\Product\AbstractProduct');

        $product_collection = $this->_produdctCollectionFactory->create()
                            ->addCategoryFilter($category)
                            ->addAttributeToSelect(array('name', 'price', 'small_image'))
                            ->addAttributeToSelect(array('special_price','show_in_menu2'))
                            ->addAttributeToSelect('status')
                            ->addAttributeToFilter('show_in_menu2', 1)
                            ->setPageSize(4)
                            ->setOrder('entity_id','desc');

         $product_collection_count=$product_collection->count();
/*echo $product_collection->getSelect();
echo '<br>';
        echo $product_collection=$product_collection->count();
exit;*/
        $product_list='';
        if( $product_collection_count>0){
        $product_list='<div class="product_list"><h3 class="sub_menu_title">Best Sellers</h3>';

            foreach ($product_collection as $key => $_product_1) {
                $product_list.='<div class="menu_product_list item">';

            $product_list.='<a href="'.$_product_1->getProductUrl().'" title="'.$this->stripTags($_product_1->getName(), null, true).'"><img src="'.$this->_imageHelper->init($_product_1, 'small_image', ['type'=>'small_image'])->keepAspectRatio(true)->resize('204','255')->getUrl().'" width="100" height="100" alt="'.$this->stripTags($_product_1->getName(), null, true).'" /></a>';

            $name=  strip_tags( $_product_1->getName());
            $name= (strlen($name)>45)?substr($name,0,45)."...":$name;
            
        $product_list.='<div class="menu_left_block"><h4 class="product-name">
            <a href="'.$_product_1->getProductUrl().'" title="'.$this->stripTags($_product_1->getName(), null, true).'">
            '.$name.'
            </a>
        </h4>'.$abstractProductBlock->getProductPrice($_product_1).'
                        </div></div>';
            }
        
            

          $product_list.='</div>';
        }

        return $product_list;
        

  }

}
