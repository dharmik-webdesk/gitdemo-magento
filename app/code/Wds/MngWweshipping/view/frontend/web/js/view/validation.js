define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Wds_MngWweshipping/js/model/validator',
        'Wds_MngWweshipping/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        shippingRatesValidator,
        shippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('wds_mngwwe_shipping', shippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('wds_mngwwe_shipping', shippingRatesValidationRules);
        return Component;
    }
);