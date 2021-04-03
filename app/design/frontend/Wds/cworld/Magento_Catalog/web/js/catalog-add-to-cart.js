define(['jquery', 'underscore', 'mage/translate', 'jquery/ui', 'Magento_Ui/js/modal/modal', 'jquery/jquery-storageapi', ], function($, _, $t, modal) {
    "use strict";
    $.widget('mage.catalogAddToCart', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: '',
        },
        _create: function() {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },
        _bindSubmit: function() {
            var self = this;
            this.element.mage('validation');
            this.element.on('submit', function(e) {
                e.preventDefault();
                if (self.element.valid()) {
                    self.submitForm($(this));
                }
            });
        },
        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },
        submitForm: function(form) {
            var addToCartButton, self = this;
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },
        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }
                    if (res.backUrl) {
                        parent.window.location.href = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector).removeClass('available').addClass('unavailable').find('span').html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
					
					var pName = '';
					$.each(form.serializeArray(), function (i, field) {
					    if(field.name == "productname"){
						   pName = field.value;
						}
					});
					$('#productnametocart').val(pName);
					
                    self.showAlert();
                }
            });
        },
        disableAddToCartButton: function(form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...');
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },
        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);
            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        },
        showAlert: function() {
            var cartMessage;
            $(document).on('ajaxComplete', function(event, xhr, settings) {
                if (settings.type.match(/get/i) && _.isObject(xhr.responseJSON)) {
                    var result = xhr.responseJSON;
                    if (_.isObject(result.messages)) {
                        var messageLength = result.messages.messages.length;
                        var message = result.messages.messages[0];
                        if (messageLength && message.type == 'success') {
                            cartMessage = message.text;
                        }
                    }
                    if (_.isObject(result.cart) && _.isObject(result.messages)) {
                        var messageLength = result.messages.messages.length;
                        var message = result.messages.messages[0];
                        if (messageLength && message.type == 'success') {
                            cartMessage = message.text;
                        }
                    }
                    
                }
            });
            setTimeout(function() {
			        var pName = '';
					var checkouturl = $('#checkouturl').val();
					if (cartMessage == null || cartMessage == 'undefined'){
					     if($('#productnametocart').val()){
					       var pName = $('#productnametocart').val();
						 }
					     var cartMessage = "You added "+pName+ " to your shopping cart.";
					}
                    var popup = $('<div class="themecafe-free-popup"/>').html(cartMessage).modal({
                        modalClass: 'changelog',
                        title: 'Information',
                        buttons: [{
                            'text': 'Checkout',
                            'class': 'btn',
                            click: function() {
                                parent.window.location = checkouturl;
                            }
                        }]
                    });
                    popup.modal('openModal');
			}, 1800);
        },
    });
    return $.mage.catalogAddToCart;
});