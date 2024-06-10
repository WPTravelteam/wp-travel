if( ( typeof _wp_travel_check_for_pro  != 'undefined' && _wp_travel_check_for_pro.is_enable == '1' ) && ( typeof _wp_travel_check_cp_by_billing != 'undefined' && _wp_travel_check_cp_by_billing.is_enable == 'yes' ) && ( typeof _wp_travel_check_cp_enable != 'undefined' && _wp_travel_check_cp_enable.is_enable == 'yes' ) ){ 
    
    jQuery(function ($) {
        $(".wp-travel-radio-group.wp-travel-payment-field .wp-travel-radio").remove();
        $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio no-payment-found'>Select billing country to get payment gateway.</div>");
        $( '#wp-travel-country' ).change( function(){
            $(".wp-travel-radio-group.wp-travel-payment-field .wp-travel-radio").remove();

            var activePaymentList = Object.keys(_wp_travel_active_payment);            
            var paymentList = [];
            var count = 1;
            for (const key in _wp_travel_conditional_payment_list) {
                if( activePaymentList.includes( _wp_travel_conditional_payment_list[key].payment_gateway ) ){
                    if( _wp_travel_conditional_payment_list[key].billing_address == this.value ){
                        paymentList[count] = _wp_travel_conditional_payment_list[key].payment_gateway;
                               
                    }
                }
               count++; 
            }

            if ( paymentList.length > 0 ) {
                paymentList.forEach(showPaymentGateway);
            }else{
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio no-payment-found'>Payment Gateway is not found for selected billing country.</div>");
            }
        
        } );

        function showPaymentGateway(item, index) {
            if( item == 'paypal' ){               
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-paypal' name='wp_travel_payment_gateway' value='paypal' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-paypal' class='radio-checkbox-label'>Standard Paypal</label></div>");
            }

            if( item == 'instamojo_checkout' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-instamojo_checkout' name='wp_travel_payment_gateway' value='instamojo_checkout' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-instamojo_checkout' class='radio-checkbox-label'>Instamojo</label></div>");
            }

            if( item == 'bank_deposit' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-bank_deposit' name='wp_travel_payment_gateway' value='bank_deposit' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-bank_deposit' class='radio-checkbox-label'>Bank Deposit</label></div>");
            }
            if( item == 'khalti' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-khalti' name='wp_travel_payment_gateway' value='khalti' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-khalti' class='radio-checkbox-label'>Khalti</label></div>");
            }

            if( item == 'payu' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-payu' name='wp_travel_payment_gateway' value='payu' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-payu' class='radio-checkbox-label'>Payu</label></div>");
            }

            if( item == 'payu_latam' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-payu_latam' name='wp_travel_payment_gateway' value='payu_latam' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-payu_latam' class='radio-checkbox-label'>Payu Latam</label></div>");
            }

            if( item == 'payfast' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-payfast' name='wp_travel_payment_gateway' value='payfast' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-payfast' class='radio-checkbox-label'>Payfast</label></div>");
            }

            if( item == 'payhere' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-payhere' name='wp_travel_payment_gateway' value='payhere' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-payhere' class='radio-checkbox-label'>Payhere</label></div>");
            }

            if( item == 'express_checkout' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-express_checkout' name='wp_travel_payment_gateway' value='express_checkout' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-express_checkout' class='radio-checkbox-label'>Paypal Express Checkout</label></div>");
            }

            if( item == 'paystack' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-paystack' name='wp_travel_payment_gateway' value='paystack' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-paystack' class='radio-checkbox-label'>Paystack</label></div>");
            }

            if( item == 'razorpay_checkout' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-razorpay_checkout' name='wp_travel_payment_gateway' value='razorpay_checkout' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-razorpay_checkout' class='radio-checkbox-label'>Razorpay</label></div>");
            }

            if( item == 'squareup_checkout' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-squareup_checkout' name='wp_travel_payment_gateway' value='squareup_checkout' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-squareup_checkout' class='radio-checkbox-label'>Squareup</label></div>");
            }

            if( item == 'stripe' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-stripe' name='wp_travel_payment_gateway' value='stripe' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-stripe' class='radio-checkbox-label'>Stripe</label></div>");
            }

            if( item == 'stripe_ideal' ){
                
                $(".wp-travel-radio-group.wp-travel-payment-field").append("<div class='wp-travel-radio'><input type='radio' id='wp-travel-payment-stripe_ideal' name='wp_travel_payment_gateway' value='stripe_ideal' data-parsley-required='1' required='1' data-parsley-errors-container='#error_container-wp-travel-payment-gateway' data-parsley-multiple='wp_travel_payment_gateway' checked><label for='wp-travel-payment-stripe_ideal' class='radio-checkbox-label'>Stripe Ideal</label></div>");
            }
        }
    });
}

jQuery(function($) {
    
    $(document).on('click', '.open-quick-view-modal', function(event) {
        event.preventDefault();

        $(this).siblings('.wp-travel-quick-view-modal').show();
        $('.modal-overlay').show();
    });

    // Close the modal
    $(document).on('click', '.close-modal', function(event) {
        event.preventDefault();

        $(this).closest('.wp-travel-quick-view-modal').hide();
        $('.modal-overlay').hide();
    });

    $(document).on('click', '.modal-overlay', function(event) {
        event.preventDefault();
        $('.wp-travel-quick-view-modal').hide();
        $('.modal-overlay').hide();
    });

    $('.wp-travel-quick-view #overview').show();
    $( '.wp-travel-quick-view ul.tab-list li' ).addClass( 'resp-tab-active' );
    $('.wp-travel-quick-view .tab-list-content').addClass( 'resp-tab-content-active' );

    $(document).on('click', '.wp-travel-quick-view ul.tab-list .overview', function(event) {
        event.preventDefault();
        $( '.wp-travel-quick-view ul.tab-list li' ).removeClass( 'resp-tab-active' );
        $( this ).addClass( 'resp-tab-active' );
        $('.wp-travel-quick-view .tab-list-content').hide();
        $('.wp-travel-quick-view #overview').show();
    });

    $(document).on('click', '.wp-travel-quick-view ul.tab-list .trip_outline', function(event) {
        event.preventDefault();
        $( '.wp-travel-quick-view ul.tab-list li' ).removeClass( 'resp-tab-active' );
        $( this ).addClass( 'resp-tab-active' );
        $('.wp-travel-quick-view .tab-list-content').hide();
        $('.wp-travel-quick-view #trip_outline').show();
    });

    $(document).on('click', '.wp-travel-quick-view ul.tab-list .trip_includes', function(event) {
        event.preventDefault();
        $( '.wp-travel-quick-view ul.tab-list li' ).removeClass( 'resp-tab-active' );
        $( this ).addClass( 'resp-tab-active' );
        $('.wp-travel-quick-view .tab-list-content').hide();
        $('.wp-travel-quick-view #trip_includes').show();
    });

    $(document).on('click', '.wp-travel-quick-view ul.tab-list .trip_excludes', function(event) {
        event.preventDefault();
        $( '.wp-travel-quick-view ul.tab-list li' ).removeClass( 'resp-tab-active' );
        $( this ).addClass( 'resp-tab-active' );
        $('.wp-travel-quick-view .tab-list-content').hide();
        $('.wp-travel-quick-view #trip_excludes').show();
    });
    
    $(document).on('click', '.wp-travel-quick-view ul.tab-list .gallery', function(event) {
        event.preventDefault();
        $( '.wp-travel-quick-view ul.tab-list li' ).removeClass( 'resp-tab-active' );
        $( this ).addClass( 'resp-tab-active' );
        $('.wp-travel-quick-view .tab-list-content').hide();
        $('.wp-travel-quick-view #gallery').show();
    });
});


jQuery(function ($) {

    
    // $( '.wp-travel-checkout-section .wp-travel-payment-field.f-full-payment .wp-travel-trip-price-figure' ).text( $('.cart-summary .wp-travel-payable-amount .wp-travel-trip-price-figure').text() );

    // $( '.wp-travel-checkout-section .wp-travel-checkout-partial-payment .wp-travel-trip-price-figure' ).text(  $('.cart-summary .total-partial .wp-travel-trip-price-figure').text() );



    // $("#wp-travel-payment-mode").change(function(){
    //     if($( '#wp-travel-payment-mode' ).val() == 'partial' ){

    //         $( '.wp-travel-checkout-partial-payment' ).css( 'visibility', 'visible' );
    //         $( '.wp-travel-checkout-partial-payment' ).css( 'height', 'auto' );
    //     }
    // });

    $('#faq #close-all').click( function(){
        $('#faq .panel-collapse.collapse').removeClass( 'show' );
    } );

    $('.trip-video').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        preloader: true,
    });

    if ($('.wp-travel-error').length > 0) {

        $('html, body').animate({
            scrollTop: ($('.wp-travel-error').offset().top - 200)
        }, 1000);

    }

    function wp_travel_set_equal_height() {
        var base_height = 0;
        $('.wp-travel-feature-slide-content').css({ 'height': 'auto' });
        var winWidth = window.innerWidth;
        if (winWidth > 992) {

            $('.wp-travel-feature-slide-content').each(function () {
                if ($(this).height() > base_height) {
                    base_height = $(this).height();
                }
            });
            if (base_height > 0) {
                $('.trip-headline-wrapper .left-plot').height(base_height); // Adding Padding of right plot.
                $('.trip-headline-wrapper .right-plot').height(base_height);
            }
        }
    }
    wp_travel_set_equal_height();

    $('.wp-travel-gallery').magnificPopup({
        delegate: 'a', // child items selector, by clicking on it popup will open
        type: 'image',
        // other options
        gallery: {
            enabled: true
        }
    });

    $('.wp-travel-send-enquiries').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#wp-travel-enquiry-name',
        midClick: true,
        callbacks: {
            open: function () {
                $('#wp-travel-enquiries').trigger('reset').parsley().reset();
            },
        }
    });

    //For New itinerary layout support.
    $('.wti-send-enquiries').magnificPopup({
        type: 'inline',
        preloader: false,
        focus: '#wp-travel-enquiry-name',
        midClick: true,
        callbacks: {
            open: function () {
                $('#wp-travel-enquiries').trigger('reset').parsley().reset();
            },
        }
    });

    $('#wp-travel-tab-wrapper').easyResponsiveTabs({});

    // Rating script starts.
    $('.rate_label').hover(function () {
        var rateLabel = $(this).attr('data-id');
        $('.rate_label').removeClass('fas');
        $('.elementor-widget-wp-travel-trip-review-form .rate_label').removeClass('fas');
        rate(rateLabel);
    },
        function () {
            var ratedLabel = $('.elementor-widget-wp-travel-trip-review-form #wp_travel_rate_val, #wp_travel_rate_val').val();

            $('.rate_label').removeClass('fas').addClass('far');
            $('.elementor-widget-wp-travel-trip-review-form .rate_label').removeClass('fas').addClass('far');
            if (ratedLabel > 0) {
                rate(ratedLabel);
            }
        });

    function rate(rateLabel) {
        for (var i = 0; i < rateLabel; i++) {
            $('.rate_label:eq( ' + i + ' )').addClass('fas').removeClass('far');
            $('.elementor-widget-wp-travel-trip-review-form .rate_label:eq( ' + i + ' )').addClass('fas').removeClass('far');
        }

        for (j = 4; j >= i; j--) {
            $('.rate_label:eq( ' + j + ' )').addClass('far');
            $('.elementor-widget-wp-travel-trip-review-form .rate_label:eq( ' + j + ' )').addClass('far');
        }
    }

    // click
    $('.rate_label').click(function (e) {
        e.preventDefault();
        $('#wp_travel_rate_val').val($(this).attr('data-id'));
        $('.elementor-widget-wp-travel-trip-review-form #wp_travel_rate_val').val($(this).attr('data-id'));
    });
    // Rating script ends.

    $(document).on('click', '.wp-travel-count-info', function (e) {
        e.preventDefault();
        $(".wp-travel-review").trigger("click");
    });

    $(document).on('click', '.top-view-gallery', function (e) {
        e.preventDefault();
        $(".wp-travel-tab-gallery-contnet").trigger("click");
    });

    $(document).on('click', '.wp-travel-count-info, .top-view-gallery', function (e) {
        e.preventDefault();
        var winWidth = $(window).width();
        var tabHeight = $('.wp-travel-tab-wrapper').offset().top;
        if (winWidth < 767) {
            var tabHeight = $('.resp-accordion.resp-tab-active').offset().top;
        }
        $('html, body').animate({
            scrollTop: (tabHeight)
        }, 1200);

    });

    // Scroll and resize event
    $(window).on("resize", function (e) {
        wp_travel_set_equal_height();
    });

    // Open All And Close All accordion.
    $('.open-all-link').click(function (e) {
        e.preventDefault();
        $('.panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
        $('.panel-collapse').addClass('in');
        // $(this).hide();
        $('.close-all-link').show();
        $('.panel-collapse').css('height', 'auto');
    });
    $('.close-all-link').click(function (e) {
        e.preventDefault();
        $('.panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
        $('.panel-collapse').removeClass('in');
        // $(this).hide();
        $('.open-all-link').show();
    });

    jQuery('.wp-travel-booking-row').hide();
    jQuery('.show-booking-row').click(function (event) {
        event.preventDefault();
        var parent = $(this).closest('li.availabily-content');

        jQuery(this).parent('.action').siblings('.wp-travel-booking-row').toggle('fast', function () {

            parent.toggleClass('opened');
        }).addClass('animate');
        jQuery(this).text(function (i, text) {
            return text === wp_travel.strings.bookings.select ? wp_travel.strings.bookings.close : wp_travel.strings.bookings.select;
        })
    });

    jQuery('.wp-travel-booking-row-fd').hide();
    jQuery('.show-booking-row-fd').click(function (event) {
        event.preventDefault();
        jQuery(this).parent('.action').parent('.trip_list_by_fixed_departure_dates_booking').siblings('.wp-travel-booking-row-fd').toggle('fast').addClass('animate');
        jQuery(this).text(function (i, text) {
            return text === wp_travel.strings.bookings.select ? wp_travel.strings.bookings.close : wp_travel.strings.bookings.select;
        })
    });

    // Multiple Pricing > Fixed Departure No, Multiple Date Off.
    jQuery('.wp-travel-pricing-dates').each(function () {
        var availabledate = jQuery(this).data('available-dates');
        if (availabledate) {
            jQuery(this).wpt_datepicker({
                language: wp_travel.locale,
                // inline: true,
                autoClose: true,
                minDate: new Date(),
                onRenderCell: function (date, cellType) {
                    if (cellType == 'day') {
                        availabledate = availabledate.map(function (d) {
                            return (new Date(d)).toLocaleDateString("en-US");
                        });
                        // availabledate = availabledate.map((d) => (new Date(d)).toLocaleDateString("en-US"));
                        isDisabled = !availabledate.includes(date.toLocaleDateString("en-US"));
                        return {
                            disabled: isDisabled
                        }
                    }
                },
            });

        } else {
            jQuery(this).wpt_datepicker({
                language: wp_travel.locale,
                minDate: new Date(),
                autoClose: true,
            });
        }

    });

    // Date picker for days and nights.
    if ('undefined' !== typeof moment) {
        $('.wp-travel-pricing-days-night').wpt_datepicker({
            language: wp_travel.locale,
            minDate: new Date(),
            autoClose: true,
            onSelect: function (formattedDate, date, inst) {
                if (date) {

                    var el = inst.$el;
                    var parent = $(el).closest('form').attr('id');
                    var next_el = ('arrival_date' === $(el).attr('name')) ? $('#' + parent + ' input[name=departure_date]') : $('#' + parent + ' input[name=arrival_date]')
                    var day_to_add = parseInt(el.data('totaldays'));
                    if (day_to_add < 1) {
                        next_el.val(formattedDate);
                        return;
                    }
                    var _moment = moment(date);
                    // var newdate = new Date( date );
                    if ('arrival_date' === $(el).attr('name')) {
                        someFormattedDate = _moment.add(day_to_add, 'days').format('YYYY-MM-DD');
                    } else {
                        // newdate.setDate( newdate.getDate() - day_to_add );
                        someFormattedDate = _moment.subtract(day_to_add, 'days').format('YYYY-MM-DD');
                    }

                    var next_el_datepicker = next_el.wpt_datepicker().data('datepicker');
                    next_el_datepicker.date = new Date(someFormattedDate);
                    next_el.val(someFormattedDate);
                }
            }
        });

        //   var departure_date = $('input[name=departure_date]').wpt_datepicker().data('datepicker');
        //   if ( 'undefined' !== typeof departure_date ) {
        //     var day_to_add = departure_date.$el.data('totaldays' );;
        //     if ( day_to_add > 0 ) {
        //       someFormattedDate = moment().add(day_to_add, 'days').format('YYYY-MM-DD');
        //       departure_date.update('minDate', new Date( someFormattedDate ))
        //     }
        //   }

        $('input[name=departure_date]').each(function () {
            //   var parent = $(this).closest('form').attr( 'id' );

            var departure_date = $(this).wpt_datepicker().data('datepicker');
            if ('undefined' !== typeof departure_date) {
                var day_to_add = departure_date.$el.data('totaldays');;
                if (day_to_add > 0) {
                    someFormattedDate = moment().add(day_to_add, 'days').format('YYYY-MM-DD');
                    departure_date.update('minDate', new Date(someFormattedDate))
                }
            }
        });



    }

    if (typeof parsley == "function") {

        $('input').parsley();

    }

    $('.login-page .message a').click(function (e) {
        e.preventDefault();
        $('.login-page form.login-form,.login-page form.register-form').animate({ height: "toggle", opacity: "toggle" }, "slow");
    });

    $('.dashboard-tab').easyResponsiveTabs({
        type: 'vertical',
        width: 'auto',
        fit: true,
        tabidentify: 'ver_1', // The tab groups identifier
        activetab_bg: '#fff', // background color for active tabs in this group
        inactive_bg: '#F5F5F5', // background color for inactive tabs in this group
        active_border_color: '#c1c1c1', // border color for active tabs heads in this group
        active_content_border_color: '#5AB1D0' // border color for active tabs contect in this group so that it matches the tab head border
    });
    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
        if ($("ul.resp-tabs-list > li#" + hash).hasClass('wp-travel-ert')) {
            lis = $("ul.resp-tabs-list > li");
            lis.removeClass("resp-tab-active");
            $("ul.resp-tabs-list > li#" + hash).addClass("resp-tab-active");
            // Tab content.
            tab_cont = $('.tab-list-content');
            tab_cont.removeClass('resp-tab-content-active').hide();
            $('#' + hash + '.tab-list-content, #wp-travel-tab-content-' + hash + '.tab-list-content').addClass('resp-tab-content-active').show();
        }
        if ($('.wp-travel-tab-wrapper').length) {
            var winWidth = $(window).width();
            var tabHeight = $('.wp-travel-tab-wrapper').offset().top;
            if (winWidth < 767) {
                var tabHeight = $('.resp-accordion.resp-tab-active').offset().top;
            }
            $('html, body').animate({
                scrollTop: (tabHeight)
            }, 1200);
        }
    }
    $('.dashtab-nav').click(function (e) {

        e.preventDefault();
        var tab = $(this).data('tabtitle');

        $('#' + tab).click();
        if ($(this).hasClass('change-password')) {
            if (!$('#wp-travel-dsh-change-pass-switch').is(':checked')) {
                $('#wp-travel-dsh-change-pass-switch').trigger('click');
            }
        }

    });

    $('#wp-travel-dsh-change-pass-switch').change(function (e) {

        $('#wp-travel-dsh-change-pass').slideToggle();

    });

    $('.wp_travel_tour_extras_toggler').click(function () {
        $(this).parents('.wp_travel_tour_extras_option_single_content').children('.wp_travel_tour_extras_option_bottom').slideToggle();
    });

    // popup
    $('.wp-travel-magnific-popup').magnificPopup({
        type: 'inline',
    });

    $('.wp-travel-payment-receipt').magnificPopup({
        type: 'image',
    });

    // Pax Picker for categorized pricing
    $(document).on('click', '.paxpicker .icon-users', function (e) {
        if ($(this).closest('.paxpicker').hasClass('is-active')) {
            $(this).closest('.paxpicker').removeClass('is-active');
        } else {
            $(this).closest('.paxpicker').addClass('is-active');
        }
    });

    $('.add-to-cart-btn').on('click', function () {
        var pricing = $(this).closest('form').find('.pricing-categories');
        var selectedPax = parseInt(pricing[0].dataset.selectedPax)
        var min_pax = parseInt(pricing[0].dataset.min)
        if (selectedPax < min_pax) {
            alert(wp_travel.strings.alert.atleast_min_pax_alert)
            $(this).attr('disabled', 'disabled').css({ 'opacity': '.5' })
        } else {
            $(this).removeAttr('disabled').removeAttr('style');
        }
    });

    $(document).on('click', '.pax-picker-plus, .pax-picker-minus', function (e) {
        e.preventDefault();
        var parent = $(this).closest('.pricing-categories');
        var parent_id = parent.attr('id');
        var pricing_form = $('#' + parent.data('parent-form-id'));
        var available_pax = parseInt(document.getElementById(parent_id).dataset.availablePax)
        var selectedPax = parseInt(document.getElementById(parent_id).dataset.selectedPax)
        var max_pax = parseInt(document.getElementById(parent_id).dataset.max)
        var min_pax = parseInt(document.getElementById(parent_id).dataset.min)

        inventoryController(this);

        function inventoryController(el) {
            var input = $(el).siblings('.paxpicker-input');
            var current_val = (input.val()) ? parseInt(input.val()) : 0;
            $('#' + parent_id).find('.available-seats').find('span').text(function () {
                // var seats = parseInt($(this).text())
                var step = parseInt(jQuery(input).attr('step'));
                if ($(el).hasClass('pax-picker-plus') && available_pax > 0) {
                    available_pax = available_pax - step;
                    selectedPax = selectedPax + step;
                    current_val = current_val + step;
                    document.getElementById(parent_id).dataset.availablePax = available_pax;
                    document.getElementById(parent_id).dataset.selectedPax = selectedPax
                    input.removeAttr('disabled').val(current_val).trigger('change')
                    return available_pax;
                } else if ($(el).hasClass('pax-picker-minus') && current_val > 0) {
                    available_pax = available_pax + step;
                    selectedPax = selectedPax - step;
                    current_val = current_val - step;
                    document.getElementById(parent_id).dataset.availablePax = available_pax;
                    document.getElementById(parent_id).dataset.selectedPax = selectedPax
                    input.removeAttr('disabled').val(current_val).trigger('change')
                    return available_pax;
                }
            })
        }

        selectedPax < min_pax && pricing_form.find('input[type=submit]').attr('disabled', 'disabled').css({ 'opacity': '.5' }) || pricing_form.find('input[type=submit]').removeAttr('disabled').removeAttr('style');
        var display_value = '';
        var pax_input = '';
        $('#' + parent_id + ' .paxpicker-input').each(function () {
            if ($(this).val() > 0) {
                var type = $(this).data('type'); // Type refers to category.
                var custom_label = $(this).data('custom');
                if ('custom' === type && '' != custom_label) {
                    type = custom_label;
                }
                var category_id = $(this).data('category-id'); // category id
                display_value += ', ' + type + ' x ' + $(this).val();
                pax_input += '<input type="hidden" name="pax[' + category_id + ']" value="' + $(this).val() + '" >';
            }
        });

        if (!display_value) {
            var display_value = $('#' + parent_id).siblings('.summary').find('.participants-summary-container').data('default');
        }
        display_value = display_value.replace(/^,|,$/g, ''); // Trim Comma(').
        $('#' + parent_id).siblings('.summary').find('.participants-summary-container').val(display_value);
        $('#' + parent_id + ' .pricing-input').html(pax_input);
    });

    /**
     * Enquiry Form. This form submission is already added in wp-travel-widgets.js
     */
    // var handleEnquirySubmission = function(e) {

    //     e.preventDefault();

    //     //Remove any previous errors.
    //     $('.enquiry-response').remove();
    //     var formData = $( '#wp-travel-enquiries' ).serializeArray();
    //     formData.push({name:'nonce',value: wp_travel.nonce});
    //     var text_processing = $('#wp_travel_label_processing').val();
    //     var text_submit_enquiry = $('#wp_travel_label_submit_enquiry').val();
    //     $.ajax({
    //         type: "POST",
    //         url: wp_travel.ajaxUrl,
    //         data: formData,
    //         beforeSend: function() {
    //             $('#wp-travel-enquiry-submit').addClass('loading-bar loading-bar-striped active').val(text_processing).attr('disabled', 'disabled');
    //         },
    //         success: function(data) {

    //             if (false == data.success) {
    //                 var message = '<span class="enquiry-response enquiry-error-msg">' + data.data.message + '</span>';
    //                 $('#wp-travel-enquiries').append(message);
    //             } else {
    //                 if (true == data.success) {

    //                     var message = '<span class="enquiry-response enquiry-success-msg">' + data.data.message + '</span>';
    //                     $('#wp-travel-enquiries').append(message);

    //                     setTimeout(function() {
    //                         jQuery('#wp-travel-send-enquiries').magnificPopup('close');
    //                         $('#wp-travel-enquiries .enquiry-response ').hide();
    //                     }, '3000');

    //                 }
    //             }

    //             $('#wp-travel-enquiry-submit').removeClass('loading-bar loading-bar-striped active').val(text_submit_enquiry).removeAttr('disabled', 'disabled');
    //             //Reset Form Fields.
    //             $('#wp-travel-enquiry-name').val('');
    //             $('#wp-travel-enquiry-email').val('');
    //             $('#wp-travel-enquiry-query').val('');

    //             return false;
    //         }
    //     });
    //     $('#wp-travel-enquiries').trigger('reset');
    // }
    // $('#wp-travel-enquiries').submit(handleEnquirySubmission);

        //New Layout JS

    // scrollspy button
    $(".scroll-spy-button").each(function() {
        $(this).on('click', function(){
            var t = $(this).data("scroll");
            $('.scroll-spy-button').removeClass('active');
            $("html, body").animate({
                scrollTop: $(t).offset().top - 70
            }, {
                duration: 500,
            });

            $(this).addClass('active');

            return false;
        })
    });

    //booking selector toggle
    $('.wti__selector-item.active').find('.wti__selector-content-wrapper').slideDown();
    $('.wti__selector-heading').on('click', function(){
        $(this).parents('.wti__selector-item').toggleClass('active'); //.siblings().removeClass('active');
        // $(this).parents('.wti__selector-item').siblings().find('.wti__selector-content-wrapper').slideUp();
        $(this).siblings('.wti__selector-content-wrapper').stop().slideToggle();
    })

    $(window).on('scroll', function(){
    var sTop = $(window).scrollTop();
    var link = $('.scroll-spy-button');
    $('.wti__tab-content-wrapper').each(function() {
        var id = $(this).attr('id'),
            offset = $(this).offset().top-100,
            height = $(this).height();
        if(sTop >= offset && sTop < offset + height) {
        link.removeClass('active');
        $('#scrollspy-buttons').find('[data-scroll="#' + id + '"]').addClass('active');
        }
    });
    })
    /**
     * =========================
     * init magnific popup
     * =========================
     */

    $(document).ready(function($) {
        $('.wti__advance-gallery-item-list').magnificPopup({
        delegate: '.gallery-item  ',
        type: 'image',
        gallery: {
            enabled: true
        }
        })
    }); 

    /**
    /**
    * =========================
    * init magnific popup end
    * =========================
    */

    /**
     * =========================
     * faq
     * =========================
     */

    $(document).ready(function(){
        $('.accordion-panel-heading').click(function(){
            $(this).next().slideToggle(500);
            $(this).toggleClass('active');
            $(this).parent().toggleClass('accordion-active');
            })
        })
    /**
     * =========================
     * faq end
     * =========================
     */ 
    jQuery('#wp-travel-tab-wrapper .resp-tabs-list').wrap('<div id="slider-tab" />');
    var slick_options = {
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 6,
        arrows: true,
        variableWidth: true,
        rows:0, // Tab issue fix
        // slide: 'li',
        cssEase: 'linear',
        slidesToScroll: 1,
    }
    if (jQuery('body').hasClass('rtl')) {
        slick_options.rtl = true;
    }
    jQuery('#wp-travel-tab-wrapper .resp-tabs-list').slick(slick_options);

    jQuery('.book-trip-date').click(function (e) { 


        jQuery('#wp-travel-tab-wrapper .tab-list-content').css( 'display', 'none' );
        
        jQuery('.resp-tab-content').removeClass( 'resp-tab-content-active' );
        jQuery('.resp-tab-item').removeClass( 'resp-tab-active' );

        jQuery('.wp-travel-booking-form').addClass( 'resp-tab-active' );
        jQuery('#booking').parent().css( 'display', 'block' );

        $('html, body').animate({
            scrollTop: $( "#booking" ).offset().top
        }, 1000);
    });
});

// New Archive page list/grid view switch
function gridView() {
	var element = document.getElementById("wptravel-archive-wrapper");
	element.classList.add("grid-view");
}
  
function listView() {
	var element = document.getElementById("wptravel-archive-wrapper");
	element.classList.remove("grid-view");
}

function viewMode( mode ) {
    
    var formData = [];
    formData.push({name:'_nonce',value: wp_travel._nonce});
    formData.push({name:'action',value: 'wptravel_view_mode'});
    formData.push({name:'mode',value: mode});
    jQuery.ajax({
        type: "POST",
        url: wp_travel.ajaxUrl,
        data: formData,
        beforeSend: function() {
            // $('#wp-travel-enquiry-submit').addClass('loading-bar loading-bar-striped active').val(text_processing).attr('disabled', 'disabled');
        },
        success: function(data) {

            if( data.success ) {
                window.location.reload();
            }
        }
    });
}
  
  
var container = document.getElementById("wp-travel-view-mode-lists");
if ( container && container.length > 0 ) {
    var btns = container.getElementsByClassName("wp-travel-view-mode");
    for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
        var current = document.getElementsByClassName("active-mode");
        current[0].className = current[0].className.replace(" active-mode", "");
        this.className += " active-mode";
    });
    }
}

/**
* jquery-match-height master by @liabru
* http://brm.io/jquery-match-height/
* License: MIT
*/

;(function(factory) { // eslint-disable-line no-extra-semi
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof module !== 'undefined' && module.exports) {
        // CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Global
        factory(jQuery);
    }
})(function($) {
    /*
    *  internal
    */

    var _previousResizeWidth = -1,
        _updateTimeout = -1;

    /*
    *  _parse
    *  value parse utility function
    */

    var _parse = function(value) {
        // parse value and convert NaN to 0
        return parseFloat(value) || 0;
    };

    /*
    *  _rows
    *  utility function returns array of jQuery selections representing each row
    *  (as displayed after float wrapping applied by browser)
    */

    var _rows = function(elements) {
        var tolerance = 1,
            $elements = $(elements),
            lastTop = null,
            rows = [];

        // group elements by their top position
        $elements.each(function(){
            var $that = $(this),
                top = $that.offset().top - _parse($that.css('margin-top')),
                lastRow = rows.length > 0 ? rows[rows.length - 1] : null;

            if (lastRow === null) {
                // first item on the row, so just push it
                rows.push($that);
            } else {
                // if the row top is the same, add to the row group
                if (Math.floor(Math.abs(lastTop - top)) <= tolerance) {
                    rows[rows.length - 1] = lastRow.add($that);
                } else {
                    // otherwise start a new row group
                    rows.push($that);
                }
            }

            // keep track of the last row top
            lastTop = top;
        });

        return rows;
    };

    /*
    *  _parseOptions
    *  handle plugin options
    */

    var _parseOptions = function(options) {
        var opts = {
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        };

        if (typeof options === 'object') {
            return $.extend(opts, options);
        }

        if (typeof options === 'boolean') {
            opts.byRow = options;
        } else if (options === 'remove') {
            opts.remove = true;
        }

        return opts;
    };

    /*
    *  matchHeight
    *  plugin definition
    */

    var matchHeight = $.fn.matchHeight = function(options) {
        var opts = _parseOptions(options);

        // handle remove
        if (opts.remove) {
            var that = this;

            // remove fixed height from all selected elements
            this.css(opts.property, '');

            // remove selected elements from all groups
            $.each(matchHeight._groups, function(key, group) {
                group.elements = group.elements.not(that);
            });

            // TODO: cleanup empty groups

            return this;
        }

        if (this.length <= 1 && !opts.target) {
            return this;
        }

        // keep track of this group so we can re-apply later on load and resize events
        matchHeight._groups.push({
            elements: this,
            options: opts
        });

        // match each element's height to the tallest element in the selection
        matchHeight._apply(this, opts);

        return this;
    };

    /*
    *  plugin global options
    */

    matchHeight.version = 'master';
    matchHeight._groups = [];
    matchHeight._throttle = 80;
    matchHeight._maintainScroll = false;
    matchHeight._beforeUpdate = null;
    matchHeight._afterUpdate = null;
    matchHeight._rows = _rows;
    matchHeight._parse = _parse;
    matchHeight._parseOptions = _parseOptions;

    /*
    *  matchHeight._apply
    *  apply matchHeight to given elements
    */

    matchHeight._apply = function(elements, options) {
        var opts = _parseOptions(options),
            $elements = $(elements),
            rows = [$elements];

        // take note of scroll position
        var scrollTop = $(window).scrollTop(),
            htmlHeight = $('html').outerHeight(true);

        // get hidden parents
        var $hiddenParents = $elements.parents().filter(':hidden');

        // cache the original inline style
        $hiddenParents.each(function() {
            var $that = $(this);
            $that.data('style-cache', $that.attr('style'));
        });

        // temporarily must force hidden parents visible
        $hiddenParents.css('display', 'block');

        // get rows if using byRow, otherwise assume one row
        if (opts.byRow && !opts.target) {

            // must first force an arbitrary equal height so floating elements break evenly
            $elements.each(function() {
                var $that = $(this),
                    display = $that.css('display');

                // temporarily force a usable display value
                if (display !== 'inline-block' && display !== 'flex' && display !== 'inline-flex') {
                    display = 'block';
                }

                // cache the original inline style
                $that.data('style-cache', $that.attr('style'));

                $that.css({
                    'display': display,
                    'padding-top': '0',
                    'padding-bottom': '0',
                    'margin-top': '0',
                    'margin-bottom': '0',
                    'border-top-width': '0',
                    'border-bottom-width': '0',
                    'height': '100px',
                    'overflow': 'hidden'
                });
            });

            // get the array of rows (based on element top position)
            rows = _rows($elements);

            // revert original inline styles
            $elements.each(function() {
                var $that = $(this);
                $that.attr('style', $that.data('style-cache') || '');
            });
        }

        $.each(rows, function(key, row) {
            var $row = $(row),
                targetHeight = 0;

            if (!opts.target) {
                // skip apply to rows with only one item
                if (opts.byRow && $row.length <= 1) {
                    $row.css(opts.property, '');
                    return;
                }

                // iterate the row and find the max height
                $row.each(function(){
                    var $that = $(this),
                        style = $that.attr('style'),
                        display = $that.css('display');

                    // temporarily force a usable display value
                    if (display !== 'inline-block' && display !== 'flex' && display !== 'inline-flex') {
                        display = 'block';
                    }

                    // ensure we get the correct actual height (and not a previously set height value)
                    var css = { 'display': display };
                    css[opts.property] = '';
                    $that.css(css);

                    // find the max height (including padding, but not margin)
                    if ($that.outerHeight(false) > targetHeight) {
                        targetHeight = $that.outerHeight(false);
                    }

                    // revert styles
                    if (style) {
                        $that.attr('style', style);
                    } else {
                        $that.css('display', '');
                    }
                });
            } else {
                // if target set, use the height of the target element
                targetHeight = opts.target.outerHeight(false);
            }

            // iterate the row and apply the height to all elements
            $row.each(function(){
                var $that = $(this),
                    verticalPadding = 0;

                // don't apply to a target
                if (opts.target && $that.is(opts.target)) {
                    return;
                }

                // handle padding and border correctly (required when not using border-box)
                if ($that.css('box-sizing') !== 'border-box') {
                    verticalPadding += _parse($that.css('border-top-width')) + _parse($that.css('border-bottom-width'));
                    verticalPadding += _parse($that.css('padding-top')) + _parse($that.css('padding-bottom'));
                }

                // set the height (accounting for padding and border)
                $that.css(opts.property, (targetHeight - verticalPadding) + 'px');
            });
        });

        // revert hidden parents
        $hiddenParents.each(function() {
            var $that = $(this);
            $that.attr('style', $that.data('style-cache') || null);
        });

        // restore scroll position if enabled
        if (matchHeight._maintainScroll) {
            $(window).scrollTop((scrollTop / htmlHeight) * $('html').outerHeight(true));
        }

        return this;
    };

    /*
    *  matchHeight._applyDataApi
    *  applies matchHeight to all elements with a data-match-height attribute
    */

    matchHeight._applyDataApi = function() {
        var groups = {};

        // generate groups by their groupId set by elements using data-match-height
        $('[data-match-height], [data-mh]').each(function() {
            var $this = $(this),
                groupId = $this.attr('data-mh') || $this.attr('data-match-height');

            if (groupId in groups) {
                groups[groupId] = groups[groupId].add($this);
            } else {
                groups[groupId] = $this;
            }
        });

        // apply matchHeight to each group
        $.each(groups, function() {
            this.matchHeight(true);
        });
    };

    /*
    *  matchHeight._update
    *  updates matchHeight on all current groups with their correct options
    */

    var _update = function(event) {
        if (matchHeight._beforeUpdate) {
            matchHeight._beforeUpdate(event, matchHeight._groups);
        }

        $.each(matchHeight._groups, function() {
            matchHeight._apply(this.elements, this.options);
        });

        if (matchHeight._afterUpdate) {
            matchHeight._afterUpdate(event, matchHeight._groups);
        }
    };

    matchHeight._update = function(throttle, event) {
        // prevent update if fired from a resize event
        // where the viewport width hasn't actually changed
        // fixes an event looping bug in IE8
        if (event && event.type === 'resize') {
            var windowWidth = $(window).width();
            if (windowWidth === _previousResizeWidth) {
                return;
            }
            _previousResizeWidth = windowWidth;
        }

        // throttle updates
        if (!throttle) {
            _update(event);
        } else if (_updateTimeout === -1) {
            _updateTimeout = setTimeout(function() {
                _update(event);
                _updateTimeout = -1;
            }, matchHeight._throttle);
        }
    };

    /*
    *  bind events
    */

    // apply on DOM ready event
    $(matchHeight._applyDataApi);

    // use on or bind where supported
    var on = $.fn.on ? 'on' : 'bind';

    // update heights on load and resize events
    $(window)[on]('load', function(event) {
        matchHeight._update(false, event);
    });

    // throttled update heights on resize events
    $(window)[on]('resize orientationchange', function(event) {
        matchHeight._update(true, event);
    });

});

jQuery(function($) {
	$('.view-content').matchHeight();

  $('#trip_outline .open-all-itinerary-link').click(function (e) {
		e.preventDefault();
		var parent = '#trip_outline';
		$(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
		$(parent + ' .panel-collapse').addClass('collapse in').css('height', 'auto');
		$(this).hide();
		$(parent + ' .close-all-itinerary-link').show();
		$(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
	});

	// Close All accordion.
	$('#trip_outline .close-all-itinerary-link').click(function (e) {
		var parent = '#trip_outline';
		e.preventDefault();
		$(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
		$(parent + ' .panel-collapse').removeClass('in').addClass('collapse');
		$(this).hide();
		$(parent + ' .open-all-itinerary-link').show();
	});
});
