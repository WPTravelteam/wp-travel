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

        jQuery('#wp-travel-payment-trip-price-initial').closest('.wp-travel-form-field ').hide();
        jQuery('#wp-travel-payment-tax-percentage-info').closest('.wp-travel-form-field ').hide();

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

        jQuery('#wp-travel-payment-trip-price-initial').closest('.wp-travel-form-field ').show();
        jQuery('#wp-travel-payment-tax-percentage-info').closest('.wp-travel-form-field ').show();

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

        jQuery('[name=wp_travel_payment_gateway]').trigger('change');
    }
}

const display_payment_mode_option = {
    partial: function() {
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');
        payment_amount_info.closest('.wp-travel-form-field ').show();
        jQuery('#wp-travel-payment-trip-price-initial').closest('.wp-travel-form-field ').show();
        jQuery('#wp-travel-payment-tax-percentage-info').closest('.wp-travel-form-field ').show();
    },
    full: function() {
        var payment_amount_info = jQuery('#wp-travel-payment-amount-info');
        payment_amount_info.closest('.wp-travel-form-field ').hide();
        jQuery('#wp-travel-payment-trip-price-initial').closest('.wp-travel-form-field ').show();
        jQuery('#wp-travel-payment-tax-percentage-info').closest('.wp-travel-form-field ').show();
    }
}

jQuery(document).ready(function($) {

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

// Get Payable Amount.
function get_payable_price(payment_mode, no_of_pax) {
    if (!payment_mode) {
        payment_mode = 'full';
    }
    if (!no_of_pax) {
        no_of_pax = 1;
    }
    var trip_price = wt_payment.trip_price; // Trip Price of single Trip
    var min_partial_payment = wt_payment.payment_amount; // Min partial payement amount of single trip. 
    var price_per = wt_payment.price_per;

    var payment_amount = trip_price;
    if (payment_mode == 'partial') {
        payment_amount = min_partial_payment;
    }

    if (price_per.toLowerCase().slice(0, 6) === 'person') {
        payment_amount = parseFloat(payment_amount) * parseFloat(no_of_pax);
        if (payment_amount.toFixed)
            payment_amount = payment_amount.toFixed(2);
        trip_price = parseFloat(trip_price) * parseFloat(no_of_pax);
        if (trip_price.toFixed) {
            trip_price = trip_price.toFixed(2);
        }
    }
    var amount = new Array();
    amount['payment_amount'] = payment_amount;
    amount['trip_price'] = trip_price;
    return amount;
}