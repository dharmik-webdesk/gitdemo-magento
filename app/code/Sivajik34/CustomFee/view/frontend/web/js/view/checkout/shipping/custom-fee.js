
define([
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',

    ], function (ko, Component, quote, priceUtils,totals) {
        'use strict';
        var show_hide_customfee_blockConfig = window.checkoutConfig.show_hide_customfee_shipblock;
        
        var fee_label = window.checkoutConfig.fee_label;         
        var custom_fee_amount = window.checkoutConfig.custom_fee_amount;

	// MG start //
        var master_fee = window.checkoutConfig.custom_fee;         
    // MG End //
        
        return Component.extend({
            totals: quote.getTotals(),
            defaults: {
                template: 'Sivajik34_CustomFee/checkout/shipping/custom-fee'
            },
            canVisibleCustomFeeBlock: show_hide_customfee_blockConfig,
            getFormattedPrice:function(index){
               if(master_fee[index]!=undefined)
                    return ko.observable(priceUtils.formatPrice(master_fee[index].custom_fee_amount, quote.getPriceFormat()))
            },
            getFeeLabel:function(index){
               if(master_fee[index]!=undefined)
                    return ko.observable(master_fee[index].fee_label)
            },
            getFeeId:function(index){
                if(master_fee[index]!=undefined || master_fee[index]!=NaN)
                    return ko.observable(master_fee[index].id)
            },
            isChecked:function(index){
                if(totals.getSegment('fee_attr')!=null || totals.getSegment('fee_attr')!=undefined){
                    if(master_fee[index]!=undefined || master_fee[index]!=NaN){
                      if(totals.getSegment('fee_attr').value==master_fee[index].id){
                        return eval(totals.getSegment('fee_attr').value);
                      }
                    }
                }    
                
            },
            canVisibleCustomFeeBlockForPrice:function(index){
                if(master_fee[index]!=undefined)
                    return ko.observable(master_fee[index].id)
                else
                    return 0;
            },


        });
    });

