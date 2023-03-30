(function($) {
    if ($.fn.tabs) {
        $('.wp-travel-tabs-wrap').tabs({
            activate: function(event, ui) {
                $(ui.newPanel).css({ display: 'table' });
                $('#wp-travel-settings-current-tab').val($(ui.newPanel).attr('id'));
            },
            create: function(event, ui) {
                $(ui.panel).css({ display: 'table' });
                $('#wp-travel-settings-current-tab').val($(ui.panel).attr('id'));
            },
            load: function(event, ui) {
            }
        });

        $(".wp-travel-marketplace-tab-wrap").tabs();


    }

    //tooltip
    $('.tooltip-area').tooltipster({
        animation: 'fade',
        side: 'right',
        theme: 'tooltipster-borderless',
        maxwidth: 6,
    });

    // Coupon General Tab.
    $('#coupon-type').change(function() {

        if ($(this).val() == 'fixed') {
            $('#coupon-currency-symbol').show();
            $('#coupon-percentage-symbol').hide();
            $('#coupon-value').removeAttr('max');
        } else {
            $('#coupon-percentage-symbol').show();
            $('#coupon-currency-symbol').hide();
            $('#coupon-value').attr('max', '100');
        }
    });

    if ($.fn.wpt_datepicker) {
        $('.wp-travel-datepicker').wpt_datepicker({
            language: 'en',
            minDate: new Date()
        });
    }
    //setup before functions
    var typingTimer; //timer identifier
    var doneTypingInterval = 5000; //time in ms, 5 second for example
    var $input = $('#coupon-code');

    //on keyup, start the countdown
    $input.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $input.on('keydown', function() {
        clearTimeout(typingTimer);
    });
    //user is "finished typing," do something
    function doneTyping() {

        var value = $input.val();
        var couponId = jQuery('#wp-travel-coupon-id').val();
        coupon_fields = {}

        coupon_fields['coupon_code'] = value;
        coupon_fields['coupon_id'] = couponId;
        coupon_fields['action'] = 'wp_travel_check_coupon_code';
        coupon_fields['_nonce'] = _wp_travel._nonce

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: coupon_fields,
            beforeSend: function() {},
            success: function(data) {
                if (!data.success) {
                    jQuery('#wp-travel-coupon_code-error').show();
                } else {
                    jQuery('#wp-travel-coupon_code-error').hide();
                }
            }
        });
    }



}(jQuery));