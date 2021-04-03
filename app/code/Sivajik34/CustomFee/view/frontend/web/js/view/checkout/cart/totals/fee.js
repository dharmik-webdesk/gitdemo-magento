define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'

], function (ko, Component, quote, priceUtils, totals) {
    'use strict';
    var show_hide_customfee_blockConfig = window.checkoutConfig.show_hide_customfee_block;
    var fee_label = window.checkoutConfig.fee_label;
    var custom_fee_amount = window.checkoutConfig.custom_fee_amount;
        // MG start //
        var master_fee = window.checkoutConfig.custom_fee;         
    // MG End //

    return Component.extend({

        totals: quote.getTotals(),
        canVisibleCustomFeeBlock: show_hide_customfee_blockConfig,
        getFormattedPrice:function(){
           return ko.observable(priceUtils.formatPrice(totals.getSegment('fee').value, quote.getPriceFormat()))
        }    ,
        getFeeLabel:function(){
            if(totals.getSegment('fee_attr')!=undefined){
            for (var key in master_fee) {
                if(master_fee[key].id==totals.getSegment('fee_attr').value){
                   return ko.observable(master_fee[key].fee_label) 
                }
            }
            }
                

        },
        isDisplayed: function () {
            return this.getFeeLabel() !== '' && this.getFeeLabel() !=undefined;
        },
        getValue: function() {
            var price = 0;

            if (this.totals() && totals.getSegment('fee')) {
                price = totals.getSegment('fee').value;
            }
            return price;
        }
    });
});
