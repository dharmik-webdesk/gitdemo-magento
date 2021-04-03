var url = window.location.hostname;
var http = window.location.protocol;

url = http+"//"+url+"/";
// for local development
//url = url+"magento2newdeploy/";

var jqueryInterval = setInterval(function(){  
    
    if(window.jQuery){
      clearInterval(jqueryInterval);      
      console.log('jQuery found!!');   
      runMyScripts(); 
  /*   jQuery(document).on('click','table#checkout-review-table .button-action',function(){runMyScriptForCheckout();});
     jQuery(document).on('click','.form-discount .action-cancel',function(){runMyScriptForCheckout();});
     jQuery(document).on('click','.form-discount .action-apply',function(){runMyScriptForCheckout();});*/
     /*jQuery('.cart-installment-onepage').hide();*/
     setInterval(function(){
     	if(window.installmentsCount && window.currencySymbolToShow && window.installmetPriceTextToShow && jQuery('.grand .price').length){
     		var grandTotalNow = parseFloat(jQuery('.grand .price').text().match(/[\d\.]/g).join(''));
     		if(grandTotalNow)
     		{
     			var installmentsCalculated = (grandTotalNow/window.installmentsCount).toFixed(2);
     			var textToShow = window.currencySymbolToShow+installmentsCalculated+' x '+window.installmentsCount+' '+window.installmetPriceTextToShow;
     			/*console.log("======textToShow======");
     			console.log(textToShow);*/
     			jQuery('.cart-installment, .cart-installment-onepage').text(textToShow); 
     		}
     	}
     },10);
     setInterval(function(){
     	if(jQuery('#checkout').length){
     		/*console.log("====In checkout====");*/
	     	if(jQuery('#splitit_paymentmethod').is(":checked")){
     			/*console.log("====In checkout===payment splitit====");*/
	     		jQuery('.cart-installment-onepage').show();
	     	} else {
     			/*console.log("====In checkout===payment other====");*/
	     		jQuery('.cart-installment-onepage').hide();
	     	}
     	}
     },10);
     }else{
      console.log('jQuery not found!!');
     }       
  }, 1000);

function runMyScripts(){
	jQuery.ajax({
		url: url + "splititpaymentmethod/showinstallmentprice/getinstallmentprice", 
		success: function(result){
			
			var numOfInstallmentForDisplay = result.numOfInstallmentForDisplay;
			// show help link
			if(result.help.splitit_paymentmethod.link != undefined){
				if(jQuery("#splitit-paymentmethod").find('a').length){
					jQuery("#splitit-paymentmethod").find('a').remove();
				}
				var helpLink = '<a style="float: none;" href="javascript:void(0);" onclick="popWin(\'' +result.help.splitit_paymentmethod.link + '\',\'' +  result.help.splitit_paymentmethod.title + '\')">'+result.help.splitit_paymentmethod.title+'</a>';
				
				jQuery("#splitit-paymentmethod").append(helpLink);	
			}
			// show help link
			if(result.help.splitit_paymentredirect.link != undefined){
				if(jQuery("#splitit-paymentredirect").find('a').length){
					jQuery("#splitit-paymentredirect").find('a').remove();
				}
				var helpLink = '<a style="float: none;" href="javascript:void(0);" onclick="popWin(\'' +result.help.splitit_paymentredirect.link + '\',\'' +  result.help.splitit_paymentredirect.title + '\')">'+result.help.splitit_paymentredirect.title+'</a>';
				
				jQuery("#splitit-paymentredirect").append(helpLink);	
			}
			if(result.isActive){
				var priceSpan = "";
				var productprice = "";
				var installments = 0;
				var currencySymbol = "";
				var installmentNewSpan = "";
				var displayInstallmentPriceOnPage = result.displayInstallmentPriceOnPage;
				window.installmentsCount = result.numOfInstallmentForDisplay;
				window.currencySymbolToShow = result.currencySymbol;
				window.installmetPriceTextToShow = result.installmetPriceText;
				// for category page only
				if(jQuery('.product-items').length && displayInstallmentPriceOnPage.indexOf("category") >= 0){
					jQuery(".product-items li").each(function(){
						priceSpan = jQuery(this).find(".price").last();
						productprice = jQuery(priceSpan).text();
						currencySymbol = result.currencySymbol;
						productprice = Number(productprice.replace(/[^0-9\.]+/g,""));
						/*productprice = jQuery(this).find('[data-price-type="finalPrice"]').attr('data-price-amount');*/
						if(parseFloat(productprice)){
							installments = (productprice/result.numOfInstallmentForDisplay).toFixed(2);
							installmentNewSpan = '<br><span class="cart-installment">'+currencySymbol+installments+' x '+result.numOfInstallmentForDisplay+' '+result.installmetPriceText+'</span>';
							jQuery(priceSpan).after(installmentNewSpan);
						}
						
					});	
				}
				// for product detail page
				if(jQuery('.product-info-price').length && displayInstallmentPriceOnPage.indexOf("product") >= 0){
					priceSpan = jQuery(".product-info-price").find(".price").last();
					productprice = jQuery(priceSpan).text();
					currencySymbol = result.currencySymbol;
					productprice = Number(productprice.replace(/[^0-9\.]+/g,""));
					productprice = jQuery(".product-info-price").find('[data-price-type="finalPrice"]').attr('data-price-amount');
					if(parseFloat(productprice)){
						installments = (productprice/result.numOfInstallmentForDisplay).toFixed(2);
						installmentNewSpan = '<br><span class="cart-installment">'+currencySymbol+installments+' x '+result.numOfInstallmentForDisplay+' '+result.installmetPriceText+'</span>';
						jQuery('.product-info-price').after(installmentNewSpan);
					}
					setInterval(function() {
                        var priceSpanPdp = jQuery(".product-info-price").find(".price").last();
                        var productpricePdp = jQuery(priceSpanPdp).text();
                        productpricePdp = Number(productpricePdp.replace(/[^0-9\.]+/g, ""));
                        var installmentsCalculatedPdp = (productpricePdp / window.installmentsCount).toFixed(2);
                        var textToShowPdp = window.currencySymbolToShow + installmentsCalculatedPdp + ' x ' + window.installmentsCount + ' ' + window.installmetPriceTextToShow;
                        jQuery('.cart-installment').text(textToShowPdp);
                    }, 10);

				}
				// for cart page only
				if((window.location.href).indexOf("checkout/cart") >= 0 && displayInstallmentPriceOnPage.indexOf("cart") >= 0){
					
					var cartPageInterval = setInterval(function(){  
		    		if(jQuery("table.totals").length){
		    			clearInterval(cartPageInterval);      
						productprice = result.grandTotal;
						currencySymbol = result.currencySymbol;
						productprice = Number(productprice.replace(/[^0-9\.]+/g,""));
						if(parseFloat(productprice)){
							installments = (productprice/result.numOfInstallmentForDisplay).toFixed(2);
							installmentNewSpan = '<br><span class="cart-installment">'+currencySymbol+installments+' x '+result.numOfInstallmentForDisplay+' '+result.installmetPriceText+'</span>';
							jQuery('table.totals tr:last').after('<tr><td>'+installmentNewSpan+'</td></tr>');
						}
		    		}else{
		    			console.log('In cart page totals not found!!');   
		    		}
			      
			      }, 3000);
					
					

				}
				// onepage checkout only
				if( (window.location.href).indexOf("checkout") >= 0 && (window.location.href).indexOf("checkout/cart") < 0 &&  displayInstallmentPriceOnPage.indexOf("checkout") >= 0){

					var checkoutOnepageInterval = setInterval(function(){  
						if(jQuery("div.iwd-grand-total-item").length){
							clearInterval(checkoutOnepageInterval);    
							productprice = result.grandTotal;
							currencySymbol = result.currencySymbol;
							productprice = Number(productprice.replace(/[^0-9\.]+/g,""));
							if(parseFloat(productprice)){
								installments = (productprice/result.numOfInstallmentForDisplay).toFixed(2);
								installmentNewSpan = '<br><span class="cart-installment-onepage">'+currencySymbol+installments+' x '+result.numOfInstallmentForDisplay+' '+result.installmetPriceText+'</span>';
								jQuery('div.iwd-grand-total-item').after(installmentNewSpan);
							}
						}
					}, 3000);	
					
					
				}
				
			}	
				
		}
	});

	// regular checkout page
	
    
    if((window.location.href).indexOf("checkout") >= 0 && (window.location.href).indexOf("checkout/cart") < 0){
    	var hashInterval = setInterval(function(){  
    		if(jQuery("table.table-totals").length){
    			clearInterval(hashInterval);      
			    console.log('# payment found!!');   
			    runMyScriptForCheckout(); 

    		}else{
    			console.log('else interval # payment not found!!');   
    		}
	      
	      }, 3000);
	     }else{
	      console.log('# payment not found!!');
	     }       
	  
}

/*jQuery('table#checkout-review-table .button-action').on('click',function(){ 
	runMyScriptForCheckout(); });*/ 

function runMyScriptForCheckout(){
	jQuery.ajax({
		url: url + "splititpaymentmethod/showinstallmentprice/getinstallmentprice", 
		success: function(result){
			
			var numOfInstallmentForDisplay = result.numOfInstallmentForDisplay;
			// show help link
			if(result.help.splitit_paymentmethod.link != undefined){
				if(jQuery("#splitit-paymentmethod").find('a').length){
					jQuery("#splitit-paymentmethod").find('a').remove();
				}
				var helpLink = '<a style="float: none;" href="javascript:void(0);" onclick="popWin(\'' +result.help.splitit_paymentmethod.link + '\',\'' +  result.help.splitit_paymentmethod.title + '\')">'+result.help.splitit_paymentmethod.title+'</a>';
				
				jQuery("#splitit-paymentmethod").append(helpLink);	
			}
			// show help link
			if(result.help.splitit_paymentredirect.link != undefined){
				if(jQuery("#splitit-paymentredirect").find('a').length){
					jQuery("#splitit-paymentredirect").find('a').remove();
				}
				var helpLink = '<a style="float: none;" href="javascript:void(0);" onclick="popWin(\'' +result.help.splitit_paymentredirect.link + '\',\'' +  result.help.splitit_paymentredirect.title + '\')">'+result.help.splitit_paymentredirect.title+'</a>';
				
				jQuery("#splitit-paymentredirect").append(helpLink);	
			}
			if(result.isActive){
				var priceSpan = "";
				var productprice = "";
				var installments = 0;
				var currencySymbol = "";
				var installmentNewSpan = "";
				var displayInstallmentPriceOnPage = result.displayInstallmentPriceOnPage;
				
				// onepage checkout only
				if(jQuery("table.table-totals").length && displayInstallmentPriceOnPage.indexOf("checkout") >= 0){
					window.installmentsCount = result.numOfInstallmentForDisplay;
					window.currencySymbolToShow = result.currencySymbol;
					window.installmetPriceTextToShow = result.installmetPriceText;
					productprice = result.grandTotal;
					currencySymbol = result.currencySymbol;
					productprice = Number(productprice.replace(/[^0-9\.]+/g,""));
					if(parseFloat(productprice)){
						installments = (productprice/result.numOfInstallmentForDisplay).toFixed(2);
						installmentNewSpan = '<br><span class="cart-installment-onepage">'+currencySymbol+installments+' x '+result.numOfInstallmentForDisplay+' '+result.installmetPriceText+'</span>';
						jQuery('table.table-totals').find('.cart-installment-onepage').closest('tr').remove();
						jQuery('table.table-totals tr:last').after('<tr><td>'+installmentNewSpan+'</td></tr>');
					}
					
				}
				
			}	
				
		}
	});
}