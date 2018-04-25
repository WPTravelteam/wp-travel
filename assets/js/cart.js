jQuery(document).ready(function($) {

    if (typeof parsley == "function") {

        $('input').parsley();

    }

    $('.add-to-cart-btn').click(function(event) {
        event.preventDefault();
        // Validate all input fields.
        var isValid = true;
        var parent = '#' + $(this).data('parent-id');

        $(parent + ' input').each(function() {
            if ($(this).parsley().validate() !== true) isValid = false;
        });
        if (isValid) {
            var cart_fields = {};
            $(parent + ' input').each(function() {
                filterby = $(this).attr('name');
                filterby_val = $(this).val();
                cart_fields[filterby] = filterby_val;
            });
            cart_fields['action'] =  'wt_add_to_cart';
            // cart_fields['nonce'] =  'wt_add_to_cart_nonce';

            $.ajax({
                type: "POST",
                url: wp_travel_frontend_vars.ajaxUrl,
                data: cart_fields,
                beforeSend: function() {},
                success: function(data) {

                }
            });
        }
    });
    // wt_remove_from_cart
    $( '.wp-travel-cart-remove' ).click( function(e) {
        e.preventDefault();
        
        if( confirm('Are you sure to remove?') ) {
            var cart_id = $(this).data( 'cart-id' );

            $.ajax({
                type: "POST",
                url: wp_travel_frontend_vars.ajaxUrl,
                data: { 'action' : 'wt_remove_from_cart', 'cart_id' : cart_id },
                beforeSend: function() {},
                success: function(data) {

                }
            });
            
        }
    } );

    // Update Cart
    $( '.wp-travel-update-cart-btn' ).click( function( e ) {
        e.preventDefault();
        var update_cart_fields = {};
        $( '.ws-theme-cart-page tr.responsive-cart' ).each( function( i ){
            pax = $(this).find( 'input[name="pax"]' ).val();
            cart_id = $(this).find( 'input[name="cart_id"]' ).val();
            
            var update_cart_field = {};
            update_cart_field['pax'] = pax;
            update_cart_field['cart_id'] = cart_id;

            update_cart_fields[i] = update_cart_field;
        } );

        $.ajax({
            type: "POST",
            url: wp_travel_frontend_vars.ajaxUrl,
            data: { update_cart_fields, 'action' : 'wt_update_cart' },
            beforeSend: function() {},
            success: function(data) {
                if ( data ) {
                    location.reload();
                }
            }
        });

    } );

});