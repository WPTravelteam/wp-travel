function GetConvertedPrice( price ) {
    var conversionRate = 'undefined' !== typeof wp_travel && 'undefined' !== typeof wp_travel.conversion_rate ? wp_travel.conversion_rate : 1;
    var _toFixed       = 'undefined' !== typeof wp_travel && 'undefined' !== typeof wp_travel.number_of_decimals ? wp_travel.number_of_decimals : 2;
    conversionRate     = parseFloat( conversionRate ).toFixed( 2 );
    return parseFloat( price * conversionRate ).toFixed( _toFixed );
}

jQuery(function($) {

    function findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        }
        return result;
    }

    $(document).ready(function() {
        var prices = [];
        if( typeof wp_travel.prices === 'object' ) {
          prices = wp_travel.prices.map(function(x) {
              return parseInt(x, 10);
          });
        }
        var min         = 0;
        var max         = 0;
        var filteredMin = 0;
        var filteredMax = 0;
        if ( prices.length > 0 ) {
            min = Math.min.apply(null, prices),
            max = Math.max.apply(null, prices);
            min = parseInt( GetConvertedPrice( min ) );
            max = parseInt( GetConvertedPrice( max ) );
        }

        if (findGetParameter('min_price')) {
            filteredMin = findGetParameter('min_price');
        } else {
            filteredMin = min;
        }
        if (findGetParameter('max_price')) {
            filteredMax = findGetParameter('max_price');
        } else {
            filteredMax = max;
        }

        // Filter Range Slider Widget.
        $(".wp-travel-range-slider").slider({
            range: true,
            min: min,
            max: max,
            values: [filteredMin, filteredMax],
            slide: function(event, ui) {
                $(".price-amount").val(wp_travel.currency_symbol + ui.values[0] + " - " + wp_travel.currency_symbol + ui.values[1]);
                $('.wp-travel-range-slider').siblings('.wp-travel-filter-price-min').val(ui.values[0]);
                $('.wp-travel-range-slider').siblings('.wp-travel-filter-price-max').val(ui.values[1]);
            }
        });
        $(".price-amount").val(wp_travel.currency_symbol + $(".wp-travel-range-slider").slider("values", 0) +
            " - " + wp_travel.currency_symbol + $(".wp-travel-range-slider").slider("values", 1));

        $(".trip-duration-calender input").wpt_datepicker({
            language: wp_travel.locale,
        });

    });

    $('.wp-travel-filter-submit-shortcode').on('click', function() {
        var view_mode = $(this).siblings('.wp-travel-filter-view-mode').data('mode');
        var pathname = $(this).siblings('.wp-travel-filter-archive-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }

        query_string = '';
        if ( window.location.search ) {
            query_string = window.location.search;
        }
        
        var full_url       = new URL( pathname + query_string );
        var search_params  = full_url.searchParams;
        var data_index = $(this).siblings('.wptravel_filter-data-index').data('index');

        $('.wp_travel_search_filters_input' + data_index).each(function() {
            var filterby = $(this).attr('name');
            var filterby_val = $(this).val();
            search_params.set( filterby, filterby_val );
            full_url.search = search_params.toString();
        });

        var new_url     = full_url.toString();
        window.location = new_url;
    });

    $('.wp-travel-filter-search-submit').on('click', function() {
        var view_mode = $(this).siblings('.wp-travel-widget-filter-view-mode').data('mode');
        var pathname = $(this).siblings('.wp-travel-widget-filter-archive-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }
        // query_string = '?';
        // var check_query_string = pathname.match(/\?/);
        // if (check_query_string) {
        //     query_string = '&';
        // }
        // var data_index = $(this).siblings('.filter-data-index').data('index');
        // $('.wp_travel_search_widget_filters_input' + data_index).each(function() {
        //     filterby = $(this).attr('name');
        //     filterby_val = $(this).val();
        //     query_string += filterby + '=' + filterby_val + '&';
        // })
        // redirect_url = pathname + query_string;
        // redirect_url = redirect_url.replace(/&+$/, '');

        // redirect_url = redirect_url + '&view_mode=' + view_mode;
        // window.location = redirect_url;

        query_string = '';
        if ( window.location.search ) {
            query_string = window.location.search;
        }
        var full_url       = new URL( pathname + query_string );
        var search_params  = full_url.searchParams;

        var data_index = $(this).siblings('.filter-data-index').data('index');
        $('.wp_travel_search_widget_filters_input' + data_index).each(function() {
            var filterby = $(this).attr('name');
            var filterby_val = $(this).val();
            // query_string += filterby + '=' + filterby_val + '&';
            search_params.set( filterby, filterby_val );
            full_url.search = search_params.toString();
        })
        var new_url     = full_url.toString();
        window.location = new_url;
    });

    // Enquiry Submission.
    var handleEnquirySubmission = function(e) {

        e.preventDefault();

        //Remove any previous errors.
        $('.enquiry-response').remove();
        var formData = $( '#wp-travel-enquiries' ).serializeArray();
        formData.push({name:'nonce',value: wp_travel.nonce});
        var text_processing = $('#wp_travel_label_processing').val();
        var text_submit_enquiry = $('#wp_travel_label_submit_enquiry').val();
        $.ajax({
            type: "POST",
            url: wp_travel.ajaxUrl,
            data: formData,
            beforeSend: function() {
                $('#wp-travel-enquiry-submit').addClass('loading-bar loading-bar-striped active').val(text_processing).attr('disabled', 'disabled');
            },
            success: function(data) {

                if (false == data.success) {
                    var message = '<span class="enquiry-response enquiry-error-msg">' + data.data.message + '</span>';
                    $('#wp-travel-enquiries').append(message);
                } else {
                    if (true == data.success) {

                        var message = '<span class="enquiry-response enquiry-success-msg">' + data.data.message + '</span>';
                        $('#wp-travel-enquiries').append(message);

                        setTimeout(function() {
                            jQuery('#wp-travel-send-enquiries').magnificPopup('close');
                            $('#wp-travel-enquiries .enquiry-response ').hide();
                        }, '3000');

                    }
                }

                $('#wp-travel-enquiry-submit').removeClass('loading-bar loading-bar-striped active').val(text_submit_enquiry).removeAttr('disabled', 'disabled');
                //Reset Form Fields.
                $('#wp-travel-enquiry-name').val('');
                $('#wp-travel-enquiry-email').val('');
                $('#wp-travel-enquiry-query').val('');

                return false;
            }
        });
        $('#wp-travel-enquiries').trigger('reset');
    }
    $('body').off('submit', '#wp-travel-enquiries')
    $('#wp-travel-enquiries').submit(handleEnquirySubmission);
    
    $(document).on( 'click', '.btn-wptravel-filter-by-shortcodes-itinerary', function(){
        var parent = $(this).parent( '.wp-travel-filter-by-heading' );
        if ( parent &&  parent.siblings( '.wp-toolbar-filter-field' ) ) {
            parent.siblings( '.wp-toolbar-filter-field, .wp-travel-filter-button' ).toggleClass( 'show-in-mobile' );

            if ( parent.siblings( '.wp-toolbar-filter-field' ).hasClass( 'show-in-mobile' ) ) {
                $(this).addClass( 'active' );
            } else {
                $(this).removeClass( 'active' );
            }
        }
    } );

    $(document).on( 'click', '.edit-trip a', function(){
        $('.checkout-trip-extras').css( 'display', 'none' );
    } );


    $(document).on( 'click', '.edit-pax-selector-qty', function(){
        var cartInputValue = document.getElementsByClassName('edit-pax-'+$(this).attr( 'data-cart' ));

       
        var cartInputValueTrip = document.getElementsByClassName('wp-trave-pax-selected-frontend-second');

        
        if( $(this).attr( 'data-allpricing' ) == 1 ){
            $flag = 1;

            if( cartInputValue.length > 0 ){
                for(var i=0;i<cartInputValue.length;i++){
                    if(parseFloat(cartInputValue[i].value) < $(this).attr( 'data-minpax' ) ){
                        $flag = 0;
                    }
                }

                if( $flag == 0 ){
                    $('.cart-edit-'+$(this).attr( 'data-cart' )).addClass( 'btn-disable' );
                }else{
                    $('.cart-edit-'+$(this).attr( 'data-cart' )).removeClass( 'btn-disable' );
                }
            }
            
            if( cartInputValueTrip.length > 0 ){
                for(var i=0;i<cartInputValueTrip.length;i++){
                    if(parseFloat(cartInputValueTrip[i].value) < $(this).attr( 'data-minpax' ) ){
                        $flag = 0;
                    }
                }
                if( $flag == 0 ){
                    $('.wp-travel-book').addClass( 'btn-disable' );
                }else{
                    $('.wp-travel-book').removeClass( 'btn-disable' );
                }
            }
           
        }else{
            var totalpax=0;
            for(var i=0;i<cartInputValue.length;i++){
                if(parseFloat(cartInputValue[i].value))
                totalpax += parseFloat(cartInputValue[i].value);
            }
    
            if( totalpax < $(this).attr( 'data-minpax' ) ){
                $('.cart-edit-'+$(this).attr( 'data-cart' )).addClass( 'btn-disable' );
            }else{
                $('.cart-edit-'+$(this).attr( 'data-cart' )).removeClass( 'btn-disable' );
            }
        }
       
    } );

    

});



// PWA
// if ("serviceWorker" in navigator) {
//     window.addEventListener("load", function() {
//       navigator.serviceWorker
//         .register("/sw.js")

//     })
//   }
