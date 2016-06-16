/**
 *  Order Fees
 *
 *  @author    motionSeed <ecommerce@motionseed.com>
 *  @copyright 2016 motionSeed. All rights reserved.
 *  @license   https://www.motionseed.com/license
 */

/* global ajaxCart */
/* global priceDisplayMethod */
(function($) {
    $(function() {
        var refreshVouchersOriginal = ajaxCart.refreshVouchers;

        ajaxCart.refreshVouchers = function(jsonData) {
            refreshVouchersOriginal(jsonData);

            if (typeof (jsonData.discounts) !== 'undefined' && jsonData.discounts.length > 0) {
                $('#ajax_block_fees_overlay').empty();
                
                for (i = 0; i < jsonData.discounts.length; i++) {
                    if (parseFloat(jsonData.discounts[i].price_float) < 0) {
                        var id_element = jsonData.discounts[i].id;
                        
                        $('#vouchers, .vouchers').append($(
                                '<tr class="bloc_cart_voucher" id="bloc_cart_voucher_' + jsonData.discounts[i].id + '">'
                                + '<td class="quantity">' + jsonData.discounts[i].quantity + 'x</td>'
                                + '<td class="name" title="' + jsonData.discounts[i].description + '">' + jsonData.discounts[i].name + '</td>'
                                + '<td class="price">' + jsonData.discounts[i].price.replace('-', '') + '</td>'
                                + '<td class="delete"></td>'
                                + '</tr>'
                                ));
                        
                        // Add fees on overlay
                        $('#ajax_block_fees_overlay').append($(
                                '<div class="layer_cart_row">'
                                + '<strong class="dark">' + jsonData.discounts[i].name + ' </strong>'
                                + '<span class="ajax_block_fees">' + jsonData.discounts[i].price.replace('-', '') + '</span>'
                                + '</div>'
                                ));
                        
                        // Update quantity
                        if (jsonData.discounts[i].unit_value_real !== '!')
                        {
                            if (priceDisplayMethod !== 0)
                                $('#cart_discount_' + id_element + ' td.cart_discount_price span:not(.price).price-discount').html(jsonData.discounts[i].unit_price.replace('-', ''));
                            else
                                $('#cart_discount_' + id_element + ' td.cart_discount_price span:not(.price).price-discount').html(jsonData.discounts[i].unit_price.replace('-', ''));
                        }

                        $('#cart_discount_' + id_element + ' td.cart_discount_delete').html(jsonData.discounts[i].quantity);
                    }
                }

                $('.vouchers').show();
            }
        };

    });
})(jQuery);
