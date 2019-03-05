jQuery(document).ready(function($) {

    if (typeof parsley == "function") {

        $('input').parsley();

    }

    $('.add-to-cart-btn').click(function(event) {
        event.preventDefault();
        // Validate all input fields.
        var isValid = true;
        var parent = '#' + $(this).data('parent-id');

        $(parent + ' input, ' + parent + ' select').each(function() {
            if ($(this).parsley().validate() !== true) isValid = false;
        });
        if (isValid) {
            var cart_fields = {};
            $(parent + ' input, ' + parent + ' select').each(function(index) {
                filterby = $(this).attr('name');
                filterby_val = $(this).val();

                    if( $(this).data('multiple') == true){
                        if( 'undefined' == typeof( cart_fields[filterby] ) ) {
                            cart_fields[filterby] = [];
                        }
                        if ( $(this).attr('type') == 'checkbox' ) {
                            if( $(this).is(':checked') ){
                                cart_fields[filterby].push(filterby_val);
                            }
                        }
                        if ( $(this).data('dependent') == true ) {
                            var pare = $(this).data('parent');
                            if( $('#'+pare).is(':checked') ){
                                cart_fields[filterby].push(filterby_val);
                            }
                        }
                    }
                else {
                    cart_fields[filterby] = filterby_val;
                }
            });
            cart_fields['action'] = 'wt_add_to_cart';
            // cart_fields['nonce'] =  'wt_add_to_cart_nonce';

            $.ajax({
                type: "POST",
                url: wp_travel.ajaxUrl,
                data: cart_fields,
                beforeSend: function() {},
                success: function(data) {
                    location.href = wp_travel.cartUrl;
                }
            });
        }
    });
    // wt_remove_from_cart
    $('.wp-travel-cart-remove').click(function(e) {
        e.preventDefault();

        if (confirm(wp_travel.strings.confirm)) {
            var cart_id = $(this).data('cart-id');

            $.ajax({
                type: "POST",
                url: wp_travel.ajaxUrl,
                data: { 'action': 'wt_remove_from_cart', 'cart_id': cart_id },
                beforeSend: function() {},
                success: function(data) {
                    location.href = wp_travel.cartUrl;
                }
            });

        }
    });

    // Update Cart
    $('.wp-travel-update-cart-btn').click(function(e) {
        e.preventDefault();
        var update_cart_fields = {};
        $('.ws-theme-cart-page tr.responsive-cart').each(function(i) {
            pax = $(this).find('input[name="pax"]').val();
            cart_id = $(this).find('input[name="cart_id"]').val();
            extra_id = false;
            extra_qty = false;

            // console.log(extra_id);

            var update_cart_field = {};
            update_cart_field['extras'] = {};
            update_cart_field['extras']['id'] = {};
            update_cart_field['extras']['qty'] = {};
            update_cart_field['pax'] = pax;
            update_cart_field['cart_id'] = cart_id;
            // update_cart_field['extras'] = {};
            // update_cart_field['extras'][i] = {};
        
            // if( extra_id ) {
            //     update_cart_field['extras'][i]['id'] = extra_id;
            // }
            // if( extra_qty ) {
            //     update_cart_field['extras'][i]['qty'] = extra_qty;
            // }

            if ( $(this).next('.child_products').find('input[name="extra_id"]').length > 0 ) {

                
                $(this).next('.child_products').find('input[name="extra_id"]').each(function(j){
                    extra_id = $(this).val();
                    update_cart_field['extras']['id'][j] = extra_id;
                });

            }
            if ( $(this).next('.child_products').find('input[name="extra_qty"]').length > 0 ) {
                
                $(this).next('.child_products').find('input[name="extra_qty"]').each(function(j){
                    extra_qty = $(this).val();
                    update_cart_field['extras']['qty'][j] = extra_qty;
                });
                
            }

            update_cart_fields[i] = update_cart_field;
        });

        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: { update_cart_fields, 'action': 'wt_update_cart' },
            beforeSend: function() {},
            success: function(data) {
                if (data) {
                    location.reload();
                }
            }
        });
    });

    // Apply Coupon
    $('.wp-travel-apply-coupon-btn').click(function(e) {
        e.preventDefault();
        var trip_ids = {};
        $('.ws-theme-cart-page tr.responsive-cart').each(function(i) {
            trip_id = $(this).find('input[name="trip_id"]').val();
            trip_ids[i] = trip_id;
        });

        var CouponCode = $('input[name="wp_travel_coupon_code_input"]').val();

        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: { trip_ids, CouponCode, 'action': 'wt_cart_apply_coupon' },
            beforeSend: function() {},
            success: function(data) {
                if (data) {
                    location.reload();
                }
            }
        });
    });

    $('.wp-travel-pax, .wp-travel-tour-extras-qty').on('change', function() {
        $('.wp-travel-update-cart-btn').removeAttr('disabled', 'disabled');
        $('.book-now-btn').attr('disabled', 'disabled');
    });


    // Checkout
    // add Traveller.
    $(document).on('click', '.wp-travel-add-traveller', function(e) {
        e.preventDefault();
        var index = $(this).parent('.text-center').siblings('.payment-content').find('.payment-traveller').length;
        var unique_index = $('.payment-content .payment-traveller:last').data('unique-index');
        if (!unique_index) {
            unique_index = index;
        } else {
            unique_index += 1;
        }
        var cart_id = $(this).data('cart-id');
        var template = wp.template('traveller-info');
        $(this).closest('.text-center').siblings('.payment-content').append(JSON.parse(template({ index: index, cart_id: cart_id, unique_index: unique_index })));
    });

    // Remove Traveller.
    $(document).on('click', '.traveller-remove', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to traveler?')) {
            $(this).closest('.payment-traveller').remove();
            $('.payment-traveller.added').each(function(i) {
                $(this).find('.traveller-index').html(i + 1);
            });
        }
    });

});