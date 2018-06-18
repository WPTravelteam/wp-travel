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

    // Sortable for Global tabs.
    $('#wp-travel-tab-content-tabs_global .wp-travel-sorting-tabs tbody').sortable({
        handle: '.wp-travel-sorting-handle'
    });

    $(document).on('keyup change', '.section_title', function() {
        var title = $(this).val();
        // alert(title);
        $(this).siblings('.wp-travel-accordion-title').html(title);
    });

    // if (!jQuery().slider)
    //     return;

    // Payment Slider JS.

    var slider = document.getElementById("minimum_partial_payout");
    var output = document.getElementById("minimum_partial_payout_output");

    output.innerHTML = slider.value; // Display the default slider value
    // Update the current slider value (each time you drag the slider handle)
    const wp_travel_range_func = function() {
        var value = this.value;

        if (this.value >= 100) {
            value = 100;
        }
        if (this.value <= 1) {
            value = 1;
        }
        value = Math.max(value, 1);
        slider.value = value;
        output.value = value;

    }

    output.onkeyup = wp_travel_range_func;

    output.oninput = wp_travel_range_func;

    slider.oninput = function() {
        var value = this.value;

        if (this.value >= 100) {
            value = 100;
        }
        if (this.value <= 1) {
            value = 1;
        }
        value = Math.max(value, 1);
        output.value = value;
        slider.value = value;
    }

    // TAX Slider JS.

    // var slider = document.getElementById("trip_tax_percentage");
    // var output = document.getElementById("trip_tax_percentage_output");

    // output.innerHTML = slider.value; // Display the default slider value
    // // Update the current slider value (each time you drag the slider handle)
    // const wp_travel_tax_percentage_range_func = function() {
    //     var value = this.value;

    //     if (this.value >= 100) {
    //         value = 100;
    //     }
    //     if (this.value <= 1) {
    //         value = 1;
    //     }
    //     value = Math.max(value, 1);
    //     slider.value = value;
    //     output.value = value;

    // }

    // output.onkeyup = wp_travel_tax_percentage_range_func;

    // output.oninput = wp_travel_tax_percentage_range_func;

    // slider.oninput = function() {
    //         var value = this.value;

    //         if (this.value >= 100) {
    //             value = 100;
    //         }
    //         if (this.value <= 1) {
    //             value = 1;
    //         }
    //         value = Math.max(value, 1);
    //         output.value = value;
    //         slider.value = value;
    //     }
    //Partial Payout Options.
    if ($('#partial_payment').is(':checked')) {
        $('#wp-travel-minimum-partial-payout').show();
    } else {
        $('#wp-travel-minimum-partial-payout').hide();
    }

    $('#partial_payment').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-minimum-partial-payout').show();
        } else {
            $('#wp-travel-minimum-partial-payout').hide();
        }
    });

    $('#wp_travel_trip_facts_enable').change(function() {
        if ($(this).is(':checked')) {
            $('#fact-app').show();
        } else {
            $('#fact-app').hide();
        }
    });



    // Tax options change function.
    // if ($('#trip_tax_enable').is(':checked')) {
    //     $('#wp-travel-tax-percentage').show();
    //     $('#wp-travel-tax-price-options').show();
    //     $('#trip_tax_percentage_output').attr('required', true)

    // } else {
    //     $('#wp-travel-tax-percentage').hide();
    //     $('#wp-travel-tax-price-options').hide();
    //     $('#trip_tax_percentage_output').attr('required', false)
    // }

    $('#trip_tax_enable').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-tax-percentage').show();
            $('#wp-travel-tax-price-options').show();
            $('#trip_tax_percentage_output').attr('required', true)
        } else {
            $('#wp-travel-tax-percentage').hide();
            $('#wp-travel-tax-price-options').hide();
            $('#trip_tax_percentage_output').attr('required', false)
        }
    });

    //Enable Paypal Field.
    if ($('#payment_option_paypal').is(':checked')) {
        $('#wp-travel-paypal-email').show();
    } else {
        $('#wp-travel-paypal-email').hide();
    }

    $('#payment_option_paypal').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-paypal-email').show();
        } else {
            $('#wp-travel-paypal-email').hide();
        }
    });

    (function($) {
        // Add Color Picker to all inputs that have 'color-field' class
        $(function() {
            $('.wp-travel-color-field').wpColorPicker();
        });
    })(jQuery);

    // Open All And Close All accordion.
    $('.open-all-link').click(function(e) {
        e.preventDefault();
        $('.panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
        $('.panel-collapse').addClass('in');
        $(this).hide();
        $('.close-all-link').show();
        $('#tab-accordion .panel-collapse').css('height', 'auto');
    });
    $('.close-all-link').click(function(e) {
        e.preventDefault();
        $('.panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
        $('.panel-collapse').removeClass('in');
        $(this).hide();
        $('.open-all-link').show();
    });

    //Facts Tab.
    jQuery(document).on('click', '.fact-open', function() {
        jQuery(this).parents('table').toggleClass('open-table');
        jQuery(this).toggleClass('hide');
    });


    jQuery(document).on('click', '.new-fact-setting-adder', function() {
        jQuery('#fact-sample-collector').append(jQuery('#sampler').html().split('$index').join(Math.random().toString(36).substring(2, 15)));
    });

    jQuery(document).on('click', '.fact-remover', function() {
        confirm('Are you sure ?') && jQuery(this).parent().parent().parent().parent().remove();
    });

    jQuery(document).on('change', '.fact-type-changer', function() {
        const val = jQuery(this).find(':selected').val();
        var index = jQuery(this).data('index');
        const multiple = jQuery('.multiple-val-' + index);
        ['single', 'multiple'].includes(val) ? multiple.fadeIn() : multiple.fadeOut();
    })

    jQuery(document).on('click', '.option-deleter', function() {
        jQuery(this).parent().remove();
    })

    jQuery(document).on('keypress', '.fact-options-list', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            if (jQuery(this).val() == '') {
                return;
            }
            const val = jQuery(this).val();
            jQuery(this).val('')
            jQuery(this).siblings('.options-holder').append('<p>' + val + '<input type="hidden" name="' + jQuery(this).attr('name') + '[]" value="' + val + '"/><span class="option-deleter"><span class="dashicons dashicons-no-alt"></span></span></p>')
        }
    });

    $('[name="trip_tax_price_inclusive"]').change(function() {

        if ('yes' === $(this).val()) {
            $('#wp-travel-tax-percentage').hide();
            $('#trip_tax_percentage_output').attr('required', false);
        } else {
            $('#wp-travel-tax-percentage').show();
            $('#trip_tax_percentage_output').attr('required', true);
        }

    });


}(jQuery));