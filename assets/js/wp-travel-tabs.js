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


}(jQuery));