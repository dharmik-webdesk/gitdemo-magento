/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AffiliatePro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    "jquery",
    "mage/translate",
    "jquery/ui",
    "Magento_Ui/js/modal/modal",
], function ($) {
    'use strict';

    $.widget('affiliatepro.banner', {

        _create: function () {
            var options = this.options,
                elbannerIdClick = '#' + options.bannerId,
                self = this;

            $(elbannerIdClick).click(function () {
                self.bannerPopup();
            });
        },
        /**
         * Bind event handlers for show banner in frontend
         * @private
         */
        bannerPopup: function () {
            var options = this.options,
                bannerId = options.bannerId,
                elpopup = '.bnlink-refer-' + bannerId;
            $(elpopup).modal({
                title: 'Link and Script Refer Banner',
                autoOpen: true,
                closed: function () {
                    location.reload();
                },
                buttons: false
            });
        }
    });

    return $.affiliatepro.banner;
});