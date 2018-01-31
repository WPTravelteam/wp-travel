const payments = {
    paypal: function() {
        jQuery('#wp-travel-book-now').show();
        jQuery('#wp-travel-book-now').siblings().hide();
        jQuery('#wp-travel-book-now').siblings('.paypal-button').hide();
    }
};

const display_booking_option = {
    booking_only: function() {
        jQuery('.payment-gateway-wrapper').hide();
        jQuery('.payment-gateway-wrapper').find('[value=paypal]').attr('checked', 'checked');

        jQuery('#wp-travel-trip-price').closest('.wp-travel-form-field ').hide();
        jQuery('#wp-travel-payment-amount').closest('.wp-travel-form-field ').hide();

        var payment_mode = jQuery("input[name='wp_travel_payment_mode']");
        var trip_price_info = jQuery('#wp-travel-trip-price_info');
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');

        payment_mode.attr('disabled', 'disabled').closest('.wp-travel-form-field ').hide();
        trip_price_info.closest('.wp-travel-form-field ').hide();
        payment_amount_info.closest('.wp-travel-form-field ').hide();

        var elem = jQuery('[name=wp_travel_book_now]');
        elem.siblings().hide();
        elem.show().val(wt_payment.book_now);
    },
    booking_with_payment: function() {
        jQuery('.payment-gateway-wrapper').show();
        jQuery('.payment-gateway-wrapper').find('[value=paypal]').attr('checked', 'checked');

        jQuery('#wp-travel-trip-price').closest('.wp-travel-form-field ').hide();
        jQuery('#wp-travel-payment-amount').closest('.wp-travel-form-field ').hide();

        var elem = jQuery('[name=wp_travel_book_now]');

        var payment_mode = jQuery("input[name='wp_travel_payment_mode']");
        var trip_price_info = jQuery('#wp-travel-trip-price_info');
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');

        payment_mode.removeAttr('disabled').closest('.wp-travel-form-field ').show();
        trip_price_info.closest('.wp-travel-form-field ').show();

        var payment_mode = jQuery("input[name='wp_travel_payment_mode']:checked").val();
        if (!payment_mode) {
            payment_mode = 'full';
        }
        payment_amount_info.closest('.wp-travel-form-field ').hide();
        if ('partial' === payment_mode) {
            payment_amount_info.closest('.wp-travel-form-field ').show();
        }
        elem.val(wt_payment.book_n_pay);
    }
}

const display_payment_mode_option = {
    partial: function() {
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');
        payment_amount_info.closest('.wp-travel-form-field ').show();
    },
    full: function() {
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');
        payment_amount_info.closest('.wp-travel-form-field ').hide();
    }
}

jQuery(document).ready(function($) {
    $('#wp-travel-pax').on('change', function() {
        var no_of_pax = $(this).val();

        if (no_of_pax < 1) {
            no_of_pax = 1;
            $(this).val(1).trigger('change')
        }

        var price_per = $('#wp-travel-trip-price').attr('price_per');
        var trip_price = $('#wp-travel-trip-price').attr('trip_price');

        // Need value from attr
        var payment_amount = $('#wp-travel-payment-amount').attr('payment_amount');

        var payment_mode = $("input[name='wp_travel_payment_mode']:checked").val();

        if (price_per.toLowerCase().slice(0, 6) === 'person') {
            trip_price = parseFloat(trip_price) * parseFloat(no_of_pax);
            payment_amount = parseFloat(payment_amount) * parseFloat(no_of_pax);
        }
        // $('#wp-travel-trip-price').val(parseFloat(trip_price));
        // $('#wp-travel-payment-amount').val(payment_amount);
        if (trip_price.toFixed)
            trip_price = trip_price.toFixed(2);
        if (payment_amount.toFixed)
            payment_amount = payment_amount.toFixed(2);
        var trip_price_info = $('#wp-travel-trip-price_info');
        var payment_amount_info = $('#wp-travel-payment-amount-info');
        trip_price_info.text(trip_price);
        payment_amount_info.text(payment_amount);
    });

    // Functions
    var gateway_change = function() {
        const func = $('[name=wp_travel_payment_gateway]:checked').val();
        const executor = payments[func];
        executor && executor();
    };
    var booking_option_change = function() {
        const trigger = $('[name=wp_travel_booking_option]:checked').val();
        display_booking_option[trigger] && display_booking_option[trigger]();
    };

    var payment_mode_change = function() {
        const trigger = $('[name=wp_travel_payment_mode]:checked').val();
        display_payment_mode_option[trigger] && display_payment_mode_option[trigger]();
    };

    // Initial Load.
    gateway_change();
    booking_option_change();

    // On Change Events.
    $('[name=wp_travel_payment_gateway]').change(gateway_change);
    $('[name=wp_travel_booking_option]').change(booking_option_change);
    $('[name=wp_travel_payment_mode]').change(payment_mode_change);

});