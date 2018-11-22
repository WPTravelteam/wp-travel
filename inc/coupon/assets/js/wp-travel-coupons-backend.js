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
                // console.log( ui );
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

    // Multiselect JS Start.
    jQuery('.select-main .close').hide();
    jQuery(document).on('click', '.select-main .close', function() {
        $(this).siblings('.wp-travel-active').removeClass('wp-travel-active');
        $(this).siblings('.carret').show();
        $(this).hide();

    });
    jQuery(document).on('click', '.select-main, .select-main .caret', function(e) {
        if ($(this).find('ul.wp-travel-active').length == 0) {
            $(this).children('ul').addClass('wp-travel-active');
            $(this).children('.close').show();
            $(this).children('.carret').hide();
        } else {
            $(this).children('.carret').show();
            $(this).children('.close').hide();
            $(this).children('ul').removeClass('wp-travel-active');
        }
    });

    $(document).on("click", function(event) {
        var $trigger = $(".select-main");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $("ul.wp-travel-active").removeClass("wp-travel-active");
            $('.select-main').children('.carret').show();
            $('.select-main').children('.close').hide();
        }
    });

    jQuery(document).on('change', '.select-main li input.multiselect-value', function($) { //on change do stuff
        var total_inputs_length = jQuery(this).closest('.select-main').find('li input.multiselect-value').length;
        var total_checked_length = jQuery(this).closest('.select-main').find('li input.multiselect-value:checked').length;
        // alert( total_inputs_length + ' - ' + total_checked_length );

        if (total_checked_length == total_inputs_length) {
            jQuery(this).closest('.select-main').find('.multiselect-all').prop('checked', true);
        } else {
            jQuery(this).closest('.select-main').find('.multiselect-all').prop('checked', false);
        }
        jQuery(this).closest('li').toggleClass('selected');

    });
    jQuery('.multiselect-all').change(function($) {
        if (!jQuery(this).is(':checked')) {
            jQuery(this).closest('li').siblings().removeClass('selected').find('input.multiselect-value').prop('checked', false);
        } else {
            jQuery(this).closest('li').siblings().addClass('selected').find('input.multiselect-value').prop('checked', true);

        }
    })
    var updateTable = function(event) {
        var currentID = jQuery(this).attr('id');
        var countSelected = jQuery(this).closest('.select-main').find('li.selected').length
        jQuery(this).closest('.select-main').find('ul').siblings('.selected-item').html(countSelected + ' item selected');
    }
    jQuery(document).on('input click change', 'input.wp-travel-multi-inner', updateTable)

    // Multipleselect JS End 


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