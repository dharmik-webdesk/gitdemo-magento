<?php

namespace Wds\ReviewNotify\Block\Adminhtml\Customreview\Edit\Tab;

/**
 * Customreview edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Wds\ReviewNotify\Model\Status
     */
    protected $_status;

    protected $_optionsCateogry;

    

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Wds\ReviewNotify\Model\OptionsCateogry $OptionsCateogry     
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Wds\ReviewNotify\Model\OptionsCateogry $OptionsCateogry,
        \Wds\ReviewNotify\Model\OptionsProduct $OptionsProduct,
        \Wds\ReviewNotify\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_optionsCateogry = $OptionsCateogry;
        $this->_optionsProduct = $OptionsProduct;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Wds\ReviewNotify\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('customreview');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
   

       
        $textbox_value=$removeButton='';
		if($model->getData('product_id')){
            $textbox_value= $this->_optionsProduct->getProductName($model->getData('product_id'));
            $removeButton='<button title="Remove" onclick="removeProduct(this)" type="button" class="action-default primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" data-ui-id="reviewnotify-review-edit-save-button"><span class="ui-button-text">
            <span>Remove Product</span></span></button>';
        }    

	    $fieldset->addField(
            'product_id',
            'text',
            [
                'name' => 'product_id',
                'required' => true,
                'label' => __('Product'),
                'style'     =>'display:none',
                'title' => __('Product Name'),
				'disabled' => $isElementDisabled,
                'after_element_html' => "<input type='text' value='".$textbox_value."' class='input-text admin__control-text' onkeypress='click_chart(this,\"page_product_id\")' onblue='hideblock(this)' />
                <div class='result_loader'></div>".$removeButton."
                <script>
                function hideblock(){
                }
                function removeProduct(obj){
                    var parent = jQuery(obj).parent().parent();
                    jQuery(parent).find('input').val('');
                    jQuery(obj).hide();
                }
                var searchdelay = (function(obj,element){
                    var timer = 0;
                    return function(callback, ms){
                        clearTimeout (timer);
                        timer = setTimeout(callback, ms);
                    };
                })();

                function click_chart(obj,element){
                    var obj2=obj;
                    var element2=element;
                    searchdelay(function(){
                        click_chart2(obj2,element2);
                    },400);
                }

                function setvalue(val,current_obj,element,obj){
                    jQuery('#'+element).val(val);
                    var html =jQuery(current_obj).text();
                    html =unescape(html);
                    jQuery(current_obj).parent().parent().prev().val(html);
                    jQuery('.result_loader').hide();
                }

                function click_chart2(obj,element) {
                    var a = obj.value;
                    var next_ele=jQuery(obj).next();
                    jQuery('.result_loader').hide();   
                    jQuery(obj).parent().find('.result_loader').html('<div class=\'loading\'>Loading...</div>');
                    jQuery(obj).parent().find('.result_loader').show();   
                    jQuery.ajax({
                        type: \"GET\",
                        dataType: \"json\",
                        contentType: \"application/json\",
                        url: \"".$this->getUrl('reviewnotify/customreview/searchajax')."\", 
                        data: \"search=\" + a,
                        success: function (result) { 
                            var html='';
                            if(result.status==1){
                                html =  html + '<ul>';
                                for(key in result.data) {
                                    var value = result.data[key];
                                    
                                    if(value.key){
                                        html =  html + '<li onclick=\"setvalue(\''+value.key+'\',this,\''+element+'\')\">'+unescape(value.name)+'</li>';
                                    }
                                }
                                html =  html + '</ul>';
                            }
                            jQuery(next_ele).html(html); 
                        },
                        error: function (error) {   }
                    });
                 }
                </script><style>
                .result_loader {background-color: white;border: 1px solid #adadad; border-bottom:0px;border-top:0px; position: absolute;z-index: 998;  top: 31px;
    width: 60%;}
                .result_loader .loading{padding:5px;border-bottom: 1px solid #adadad;}
                .result_loader ul{list-style:none;margin: 0;padding: 0;z-index: 999;}
                .result_loader ul li {padding:5px;border-bottom: 1px solid #adadad; cursor: pointer;}
                </style> <bR /><i>Enter keywork to search product by sku</i>",
            ]
        );




        $fieldset->addField(
            'customer_name',
            'text',
            [
                'name' => 'customer_name',
                'required' => true,
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
                'disabled' => $isElementDisabled
            ]
        );      

        $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'required' => true,
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
                'label' => __('Message'),
                'title' => __('Message'),
                'disabled' => $isElementDisabled
            ]
        );
       							
		if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
}
