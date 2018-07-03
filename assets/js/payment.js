const display_booking_option = {
    booking_only: function() {
        jQuery('.wp-travel-payment-field').hide().find('input, select').attr('disabled', 'disabled');
        jQuery('.f-booking-only-field').show().find('input, select').removeAttr('disabled');
        var elem = jQuery('[name=wp_travel_book_now]');
        elem.siblings().hide();
        elem.show().val(wt_payment.book_now);
    },
    booking_with_payment: function() {
        jQuery('.wp-travel-payment-field').hide().find('input, select').attr('disabled', 'disabled');
        jQuery('.f-booking-with-payment').show().find('input, select').removeAttr('disabled');

        // Trigger Payment mode.
        var payment_mode = jQuery("select[name='wp_travel_payment_mode']").val();
        display_payment_mode_option[payment_mode] && display_payment_mode_option[payment_mode]();

        var elem = jQuery('[name=wp_travel_book_now]');
        // elem.siblings().hide();
        elem.show().val(wt_payment.book_n_pay);
    }
}
const payments = {
    paypal: function() {
        jQuery('#wp-travel-book-now').show();
        jQuery('#wp-travel-book-now').siblings().hide();
        jQuery('#wp-travel-book-now').siblings('.paypal-button').hide();
    }
};


const display_payment_mode_option = {
    partial: function() {
        jQuery('.wp-travel-payment-field').hide().find('input, select').attr('disabled', 'disabled');
        jQuery('.f-partial-payment').show().find('input, select').removeAttr('disabled');
    },
    full: function() {
        jQuery('.wp-travel-payment-field').hide().find('input, select').attr('disabled', 'disabled');
        jQuery('.f-full-payment').show().find('input, select').removeAttr('disabled');

        // cart fields override
        jQuery('td.f-partial-payment, th.f-partial-payment').hide();
    }
}

jQuery(document).ready(function($) {

    // Functions
    var booking_option_change = function() {
        const trigger = $('[name=wp_travel_booking_option]').val();
        display_booking_option[trigger] && display_booking_option[trigger]();
    };

    var gateway_change = function() {
        const func = $('[name=wp_travel_payment_gateway]:checked').val();
        const executor = payments[func];
        executor && executor();
    };

    var payment_mode_change = function() {
        const trigger = $('[name=wp_travel_payment_mode]').val();
        display_payment_mode_option[trigger] && display_payment_mode_option[trigger]();
        const func = $('[name=wp_travel_payment_gateway]:checked').val();
        const executor = payments[func];
        executor && executor();
    };

    // Initial Load.
    booking_option_change();
    gateway_change();
    payment_mode_change();

    // On Change Events.
    $('[name=wp_travel_booking_option]').change(booking_option_change);
    $('[name=wp_travel_payment_gateway]').change(gateway_change);
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
    console.log(wt_payment);
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