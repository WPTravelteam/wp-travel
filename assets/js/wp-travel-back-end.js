(function($) {
    if ('undefined' != typeof(GMaps) && $('#gmap').length > 0) {
        var map = new GMaps({
                div: '#gmap',
                lat: wp_travel_drag_drop_uploader.lat,
                lng: wp_travel_drag_drop_uploader.lng
            }),
            input = document.getElementById('search-input'),
            autocomplete = new google.maps.places.Autocomplete(input);

        map.setCenter(wp_travel_drag_drop_uploader.lat, wp_travel_drag_drop_uploader.lng);
        map.setZoom(15);
        map.addMarker({
            lat: wp_travel_drag_drop_uploader.lat,
            lng: wp_travel_drag_drop_uploader.lng,
            title: wp_travel_drag_drop_uploader.loc,
            draggable: true,
            dragend: function(e) {
                var lat = e.latLng.lat();
                var lng = e.latLng.lng();
                map.setCenter(lat, lng);

                var latlng = new google.maps.LatLng(lat, lng);
                var geocoder = geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $('#wp-travel-lat').val(lat);
                            $('#wp-travel-lng').val(lng);
                            $('#wp-travel-location').val(results[1].formatted_address);
                            $('#search-input').val(results[1].formatted_address);
                        }
                    }
                });

            }
        });

        autocomplete.bindTo('bounds', map);
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            map.removeMarkers();
            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(15);
            }
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();

            var latlng = new google.maps.LatLng(lat, lng);
            var geocoder = geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $('#wp-travel-lat').val(lat);
                        $('#wp-travel-lng').val(lng);
                        $('#wp-travel-location').val(results[1].formatted_address);
                        $('#search-input').val(results[1].formatted_address);
                    }
                }
            });

            map.addMarker({
                lat: lat,
                lng: lng,
                title: place.formatted_address,
                draggable: true,
                dragend: function(e) {
                    var lat = e.latLng.lat();
                    var lng = e.latLng.lng();
                    map.setCenter(lat, lng);

                    var latlng = new google.maps.LatLng(lat, lng);
                    var geocoder = geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 'latLng': latlng }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $('#wp-travel-lat').val(lat);
                                $('#wp-travel-lng').val(lng);
                                $('#wp-travel-location').val(results[1].formatted_address);
                                $('#search-input').val(results[1].formatted_address);
                            }
                        }
                    });

                }
            });

        });
    }

    /*
     * Tab js.
     */
    if ($.fn.tabs) {
        $('.wp-travel-tabs-wrap').tabs({
            activate: function(event, ui) {
                $(ui.newPanel).css({ display: 'inline-block' });
                $('#wp-travel-settings-current-tab').val($(ui.newPanel).attr('id'));
                if ('undefined' != typeof(GMaps) && $('#gmap').length > 0) {
                    map.refresh();
                    google.maps.event.trigger(map.map, "resize");
                    // map.setCenter(wp_travel_drag_drop_uploader.lat, wp_travel_drag_drop_uploader.lng);
                }
                // wp_travel_backend_map_holder();
            },
            create: function(event, ui) {
                $(ui.panel).css({ display: 'inline-block' });
                $('#wp-travel-settings-current-tab').val($(ui.panel).attr('id'));
            },
            load: function(event, ui) {}
        });
    }

    function dateTimePicker() {

        if ($.fn.wpt_datepicker) {
            $('#wp-travel-start-date').wpt_datepicker({
                language: 'en',
                minDate: new Date(),
                onSelect: function(dateStr) {
                    newMinDate = null;
                    newMaxDate = new Date();
                    if ('' !== dateStr) {
                        // milliseconds = moment( dateStr, wp_travel_drag_drop_uploader.moment_date_format ).format( 'MM/DD/YYYY' );
                        milliseconds = moment( dateStr, 'YYYY-MM-DD' );

                        new_date_min = new Date(milliseconds);
                        newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate())));
                    }
                    $('#wp-travel-end-date').wpt_datepicker({
                        minDate: newMinDate,
                    });
                }
            });

            $('#wp-travel-end-date').wpt_datepicker({
                language: 'en',
                minDate: new Date()
            });

            $('.wp-travel-datepicker').wpt_datepicker({
                language: 'en',
                minDate: new Date()
            });

            $('.wp-travel-timepicker').wpt_datepicker({
                language: 'en',
                timepicker: true,
                onlyTimepicker: true,

            });
        }
    }
    dateTimePicker();

    $(document).on('click', '#publish', function() {

        var start_date = $('#wp-travel-start-date').val();
        var end_date = $('#wp-travel-end-date').val();

        var error = '';
        if ('' != start_date || '' != end_date) {
            if ('' == start_date) {
                error += 'Start date can\'t be empty!' + "\n";
            }
            // if ('' == end_date) {
            //     error += 'End date can\'t be empty!' + "\n";
            // }

            // if ('' != start_date && '' != end_date) {
            //     start_date = new Date(start_date);
            //     end_date = new Date(end_date);

            //     if (end_date <= start_date) {
            //         error += 'End date must greater than start date.' + "\n";
            //     }
            // }

        }

        if ('' == error) {
            $(document).off('click', '#publish');
        } else {
            alert(error);
            return false;
        }
    });

    var createAllErrors = function() {
        var form = $(this);
        // errorList = $( "ul.errorMessages", form );

        var showAllErrorMessages = function() {
            //errorList.empty();

            // Find all invalid fields within the form.
            var invalidFields = form.find(":invalid").each(function(index, node) {

                // Find the field's corresponding label
                var label = $("label[for=" + node.id + "] "),
                    // Opera incorrectly does not fill the validationMessage property.
                    message = node.validationMessage || 'Invalid value.';

                //errorList
                //  .removeClass( 'hidden' )
                alert('Error in "' + label.html() + '": ' + message);

                var cur_tab = $(this).closest('.wp-travel-tab-content');
                var tab_nav = cur_tab.attr('id');
                cur_tab.siblings().addClass( 'hidden' );
                cur_tab.removeClass( 'hidden' );

                $("a[href = #" + tab_nav + "]").trigger('click');

            });
        };

        // Support Safari
        form.on("submit", function(event) {
            if (this.checkValidity && !this.checkValidity()) {
                $(this).find(":invalid").first().focus();
                event.preventDefault();
            }
        });

        $("#publish", form)
            .on("click", showAllErrorMessages);

        $("input", form).on("keypress", function(event) {
            var type = $(this).attr("type");
            if (/date|email|month|number|search|tel|text|time|url|week/.test(type) &&
                event.keyCode == 13) {
                showAllErrorMessages();
            }
        });
    };

    // $("form").each(createAllErrors);

    $(document).on('click', '#wp-travel-enable-sale', function() {
        show_sale_price_field();
    });

    // Slugify the text string.
    function wp_travel_filter_string(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/\,+/g, '-') // Replace comma with -
            .replace(/\"+/g, '-') // Replace " with -
            .replace(/\'+/g, '-') // Replace ' with -
            .replace(/\/+/g, '-') // Replace / with -
            .replace(/\(+/g, '-') // Replace / with -
            .replace(/\)+/g, '-') // Replace / with -
            // .replace(/[^\w\-]+/g, '') // Remove all non-word chars  // issue in other language if uncomment.
            .replace(/\-\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
    }

    // @since   1.7.6
    // Pricing Option [single-price || multiple-price]
    $( document ).on( 'change', '#wp-travel-pricing-option-type', function() {
        show_price_option_row();
        // show / hide fixed departure fields.
        show_fixed_departured_date_row();
        show_minimum_payout_row();
    } );

    /**
     * @since 1.8.9
     */
    function show_price_option_row() {
        var price_option_type = $( '#wp-travel-pricing-option-type' ).val();
        var show_fields = '.' + price_option_type + '-option-row';

        $( '.price-option-row' ).addClass( 'hidden' );
        $( show_fields ).removeClass( 'hidden' );

        show_sale_price_field();
        show_custom_payout_field();
    }

    // @since 1.7.6
    function show_fixed_departured_date_row() {

        var price_option_type = $( '#wp-travel-pricing-option-type' ).val();

        var fixed_departure = $('#wp-travel-fixed-departure');

        // Default fixed departure field show /hide
        if (  'multiple-price' === price_option_type || 'single-price' === price_option_type ) {
            if ( fixed_departure.is(':checked') ) {
                $('.wp-travel-fixed-departure-row').removeClass('hidden');
                $('.wp-travel-trip-duration-row').addClass('hidden');
            } else {
                $('.wp-travel-fixed-departure-row').addClass('hidden');
                $('.wp-travel-trip-duration-row').removeClass('hidden');
            }
        }

        // Display Enable Multiple Date if Pricing option set to Multiple Price.
        $(".wp-travel-enable-multiple-dates").addClass('hidden');
        if ( 'multiple-price' === price_option_type ) {

            // Fixed Departure field override.
            if ( fixed_departure.is(':checked')  ) {
                // Show Multiple Dates fields.
                $(".wp-travel-enable-multiple-dates, #wp-variations-multiple-dates").removeClass('hidden');

                // Enable Multiple Dates override.
                show_multiple_dates_fields();

            } else {
                // Hide Multiple Dates fields.
                $(".wp-travel-enable-multiple-dates, #wp-variations-multiple-dates").addClass('hidden');
            }
        }
    }

    // @since 1.7.6
    function show_multiple_dates_fields() {
        var price_option_type = $( '#wp-travel-pricing-option-type' ).val();
        var enable_fixed_departure = $('#wp-travel-fixed-departure');
        var enable_multiple_fixed_departure = $('#wp-travel-enable-multiple-fixed-departure');

        if ( 'multiple-price' === price_option_type && enable_fixed_departure.is(':checked') && enable_multiple_fixed_departure.is( ':checked' ) ) {
            $('.wp-travel-fixed-departure-row').addClass('hidden');
            $( '#wp-variations-multiple-dates' ).removeClass( 'hidden' );
        } else {
            $('.wp-travel-fixed-departure-row').removeClass('hidden');
            $( '#wp-variations-multiple-dates' ).addClass( 'hidden' );
        }
    }
    function show_sale_price_field() {
        var price_option_type = $( '#wp-travel-pricing-option-type' ).val();
        if ( 'single-price' ==  price_option_type && $('#wp-travel-enable-sale').is(':checked')) {
            //
            $('#wp-travel-sale-price').removeAttr('disabled').closest('.price-option-row').removeClass( 'hidden' );
            $('#wp-travel-price').attr('required', 'required');
            $('#wp-travel-sale-price').attr('required', 'required');
        } else {
            $('#wp-travel-sale-price').attr('disabled', 'disabled').closest('.price-option-row').addClass( 'hidden' );
            $('#wp-travel-price').removeAttr('required');
            $('#wp-travel-sale-price').removeAttr('required');
        }

    }
    function show_custom_payout_field() {
        if ($('#wp-travel-minimum-partial-payout-percent-use-global').is(':checked')) {
            $('#wp-travel-minimum-partial-payout-percent').closest('.price-option-row').addClass( 'hidden' );
        } else {
            $('#wp-travel-minimum-partial-payout-percent').closest('.price-option-row').removeClass( 'hidden' );
        }
    }

    function show_sale_price_field() {
        var price_option_type = $( '#wp-travel-pricing-option-type' ).val();
        if ( 'single-price' ==  price_option_type && $('#wp-travel-enable-sale').is(':checked')) {
            $('#wp-travel-sale-price').removeAttr('disabled').closest('.price-option-row').removeClass( 'hidden' );
            $('#wp-travel-price').attr('required', 'required');
            $('#wp-travel-sale-price').attr('required', 'required');
        } else {
            $('#wp-travel-sale-price').attr('disabled', 'disabled').closest('.price-option-row').addClass( 'hidden' );
            $('#wp-travel-price').removeAttr('required');
            $('#wp-travel-sale-price').removeAttr('required');
        }

    }

    function show_minimum_payout_row() {
        $( '.custom-payout-option-row' ).each( function(){
            if ( $(this).hasClass( 'global-enabled' ) ) {
                $(this).removeClass( 'hidden' );
            } else {
                $(this).addClass( 'hidden' );
            }
        } );
    }

    // Fixed Departure [ On || Off ]  @since 1.7.6
    $(document).on('click', '#wp-travel-fixed-departure', function() {
        show_fixed_departured_date_row();
    });

    // Enable Multiple Dates Field @since 1.7.6
    $(document).on('change', '#wp-travel-enable-multiple-fixed-departure', function() {
        show_multiple_dates_fields();
    });

    show_price_option_row(); // single | multiple price fields
    show_fixed_departured_date_row();

    show_minimum_payout_row();
    // show_multiple_dates_fields();
    $(document).on( 'click', '.wp-travel-clone-post', function(e) {
        e.preventDefault();
        var post_id = $(this).data('post_id');
        var security = $(this).data('security');

        var data = {
            post_id : post_id,
            security: security,
            action:'wp_travel_clone_trip'
        }
        $.ajax({
            url: ajaxurl,
            data: data,
            type: "post",
            dataType: "json",
            success: function(data) {

                location.href = location.href;
                // location.reload();
            }
        });
    } );

    //Pricing Key slugify.
    $(document).on('change', '.wp-travel-variation-pricing-name', function() {

        var price_key = wp_travel_filter_string($(this).val());

        $(this).siblings('.wp-travel-variation-pricing-uniquekey').val(price_key)

    });

    // Pricing options change function. removed @since 3.0.0
    // $(document).on('change', '.wp-travel-pricing-options-list', function() {
    //     if ($(this).val() === 'custom') {
    //         $(this).parents('.price-option-row').next('.custom-pricing-label-wrap').css('display', 'block');
    //     } else {
    //         $(this).parents('.price-option-row').next('.custom-pricing-label-wrap').css('display', 'none');
    //     }
    // });

    // Pricing options change function.
    // Added since v3.0.0
    $(document).on('change', '.wp-travel-pricing-options-list', function() {
        var category_id = $(this).data('category-id');
        if ($(this).val() === 'custom') {
            $('.custom-pricing-label-wrap-' + category_id ).css('display', 'block');
        } else {
            $('.custom-pricing-label-wrap-' + category_id ).css('display', 'none');
        }
    });
    $('.wp-travel-pricing-options-list').trigger('change');



    //Pricing options Enable Sale.
    $(document).on('change', '.wp-travel-enable-variation-price-sale', function() {
        var siblings = $(this).closest('.repeat-row').next('.repeat-row');
        if ($(this).is(':checked')) {
            siblings.removeClass( 'hidden' );
            siblings.find('input[type="number"]').attr('required', 'required');
        } else {
            siblings.addClass( 'hidden' );
            siblings.find('input[type="number"]').removeAttr('required');

        }
    });

    if ($('.wp-travel-enable-variation-price-sale').is(':checked')) {
        $(this).closest('.repeat-row').next('.repeat-row').removeClass( 'hidden' );
    } else {
        $(this).closest('.repeat-row').next('.repeat-row').addClass( 'hidden' );
    }

    // New Pricing options Enable Sale.
    $(document).on('change', '.wp-travel-enable-variation-price-sale-new', function() {
        var siblings = $(this).closest('.repeat-row').next('.repeat-row');
        if ($(this).is(':checked')) {
            siblings.removeClass( 'visibility-hidden' );
            siblings.find('input[type="number"]').attr('required', 'required');
        } else {
            siblings.addClass( 'visibility-hidden' );
            siblings.find('input[type="number"]').removeAttr('required');

        }
    });

    if ($('.wp-travel-enable-variation-price-sale-new').is(':checked')) {
        $(this).closest('.repeat-row').next('.repeat-row').removeClass( 'visibility-hidden' );
    } else {
        $(this).closest('.repeat-row').next('.repeat-row').addClass( 'visibility-hidden' );
    }

    if ($('#wp-travel-enable-sale').is(':checked')) {
        $('#wp-travel-price').attr('required', 'required');
        $('#wp-travel-sale-price').attr('required', 'required');
    }



    $(document).on("click", ".wp-travel-featured-post", function(e) {
        e.preventDefault();
        var featuredIcon = $(this);
        var post_id = $(this).attr("data-post-id");
        var nonce = $(this).attr("data-nonce");
        var data = { action: "wp_travel_featured_post", post_id: post_id, nonce: nonce };
        $.ajax({
            url: ajaxurl,
            data: data,
            type: "post",
            dataType: "json",
            success: function(data) {
                if (data != 'invalid') {
                    featuredIcon.removeClass("dashicons-star-filled").removeClass("dashicons-star-empty");
                    if (data.new_status == "yes") {
                        featuredIcon.addClass("dashicons-star-filled");
                    } else {
                        featuredIcon.addClass("dashicons-star-empty");
                    }
                }
            }
        });
    });
    // Add itineraries Data Row.
    $('#add_itinerary_row').click(function(e) {
        e.preventDefault();
        var template = wp.template('wp-travel-itinerary-items');
        var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
        $('.itinerary_block').append(template({ random: rand }));

        $('.itinerary_block .panel:last .wp-travel-datepicker').wpt_datepicker({
            language: 'en',
            minDate: new Date()
        });
        $('.itinerary_block .panel:last .wp-travel-timepicker').wpt_datepicker({
            language: 'en',
            timepicker: true,
            onlyTimepicker: true,

        });

        $('.while-empty').addClass( 'hidden' );
        $('.wp-collapse-open').removeClass( 'hidden' );

    });
    //Remove Itinerary Data Row.

    $(document).on('click', '.remove_itinery', function(e) {
        e.preventDefault();
        $(this).closest('.itinerary_wrap').remove();
        return false;
    });
    var textareaID;
    $('.tab-accordion .wp-travel-sorting-tabs, #tab-accordion .wp-travel-sorting-tabs, #tab-accordion-itineraries #accordion-itinerary-data').sortable({
        handle: '.wp-travel-sorting-handle',
        // start: function(event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
        //     textareaID = $(ui.item).find('.wp-editor-container textarea').attr('id');
        //     try { tinyMCE.execCommand('mceRemoveEditor', false, textareaID); } catch (e) {}
        // },
        // stop: function(event, ui) { // re-initialize TinyMCE when sort is completed
        //     try { tinyMCE.execCommand('mceAddEditor', false, textareaID); } catch (e) {}
        //     $(this).find('.update-warning').removeClass( 'hidden' );
        // }
    });

    $('#wp-travel-tab-content-setting .wp-travel-sorting-tabs tbody').sortable({
        handle: '.wp-travel-sorting-handle',
    });

    // return on clicking space button.
    $.ui.accordion.prototype._originalKeyDown = $.ui.accordion.prototype._keydown;
    $.ui.accordion.prototype._keydown = function(event) {
        var keyCode = $.ui.keyCode;
        if (event.keyCode == keyCode.SPACE) {
            return;
        }
        this._originalKeyDown(event);
    };



    $('.ui-accordion-header').click(function() {
        $('.open-all-link').removeClass( 'hidden' );
        $('.close-all-link').removeClass( 'hidden' );
    });

    $(document).on('click', '.wt-accordion-close', function(e) {
        var acc_id = $(this).closest('.tab-accordion').attr('id');
        if (confirm("Are you sure you want to delete?") == true) {
            $(this).closest('div.panel-default').remove();

            var faqs = $('#' + acc_id + ' .panel-default:visible').length;

            // alert(faqs);
            if (faqs > 0) {
                $('.while-empty').addClass( 'hidden' ).hide();
                $('.wp-collapse-open').removeClass( 'hidden' ).show();
            } else {
                $('.wp-collapse-open').addClass( 'hidden' ).hide();
                $('.while-empty').removeClass( 'hidden' ).show();
            }
        }
        return;
    });

    $('.wp-travel-faq-add-new').on('click', function() {
        var template = wp.template('wp-travel-faq');
        var faqs = $('#accordion-faq-data panel-default').length;
        var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
        $('#accordion-faq-data').append(template({ random: rand }));

        $('.while-empty').addClass( 'hidden' );
        $('.wp-collapse-open').removeClass( 'hidden' );
        // $('#tab-accordion').accordion('destroy').accordion({ active: faqs });
    });

    // Pricing options template.
    $('.wp-travel-pricing-add-new').on('click', function() {
        var template = wp.template('wp-travel-pricing-options');
        var rand = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        var category_id = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        $('#pricing-options-data').append(template({ random: rand, category_id : category_id }));
    });

    // Trips Facts template.
    $('.wp-travel-trip-facts-add-new').on('click', function() {
        var template = wp.template('wp-travel-trip-facts-options');
        var rand = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        $('#accordion-fact-data').append(template({ random: rand }));


    });

    // Dates options template.
    $('#date-options-data .panel .wp-travel-multiple-start-date').each(function() {
        var th = $(this);

        var savedMeanDate = $(this).val();
        newMinDate = new Date();
        if ( '' !== savedMeanDate) {
            // milliseconds = moment( savedMeanDate, wp_travel_drag_drop_uploader.moment_date_format ).format( 'MM/DD/YYYY' );
            milliseconds = moment( savedMeanDate, 'YYYY-MM-DD' );

            new_date_min = new Date(milliseconds);
            newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate())));

            $(this).siblings('.wp-travel-multiple-end-date').wpt_datepicker({
                minDate: newMinDate,
            });
        }

        $(this).wpt_datepicker({
            language: 'en',
            minDate: new Date(),
            onSelect: function(dateStr) {
                newMinDate = null;
                newMaxDate = new Date();
                if ('' !== dateStr) {
                    // milliseconds = moment( dateStr, wp_travel_drag_drop_uploader.moment_date_format ).format( 'MM/DD/YYYY' );
                    milliseconds = moment( dateStr, 'YYYY-MM-DD' );

                    new_date_min = new Date(milliseconds);
                    newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate())));
                }
                th.siblings('.wp-travel-multiple-end-date').wpt_datepicker({
                    minDate: newMinDate,
                });
            }
        });

    });

    $('.wp-travel-multiple-dates-add-new').on('click', function() {
        var template = wp.template('wp-travel-multiple-dates');
        var rand = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        $('#date-options-data').append(template({ random: rand }));

        $('#date-options-data .panel:last .wp-travel-multiple-start-date').wpt_datepicker({
            language: 'en',
            minDate: new Date(),
            onSelect: function(dateStr) {
                newMinDate = null;
                newMaxDate = new Date();
                if ('' !== dateStr) {
                    // milliseconds = moment( dateStr, wp_travel_drag_drop_uploader.moment_date_format ).format( 'MM/DD/YYYY' );
                    milliseconds = moment( dateStr, 'YYYY-MM-DD' );
                    new_date_min = new Date(milliseconds);
                    newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate())));
                }
                $('#date-options-data .panel:last .wp-travel-multiple-end-date').wpt_datepicker({
                    minDate: newMinDate,
                });
            }
        });
    });

    //value bind to label.
    $(document).on('change keyup', "*[bind]", function(e) {
        var to_bind = $(this).attr('bind');
        var value = ('' != $(this).val()) ? $(this).val() : 'Untitled';
        $("*[bind='" + to_bind + "']").html(value);
        $("*[bind='" + to_bind + "']").val($(this).val());
    });

    //Sale price binding on pricing options.
    $(document).on('change keyup', "*[bindPrice]", function(e) {
        var bound_sale = $(this).attr('bindPrice');
        var value = ('' != $(this).val()) ? $(this).val() : 1;
        $("*[bindSale='" + bound_sale + "']").attr('max', value);
    });

    $(document).on('keyup change', '.section_title', function() {
        var title = $(this).val();
        $(this).siblings('.wp-travel-accordion-title').html(title);
    });

    // Sale Price  max value update on price change
    $(document).on('keyup change', '#wp-travel-price', function() {
        var priceVal = $(this).val();
        $('#wp-travel-sale-price').attr('max', priceVal);
    });

    // Sale Price  max value update on price change
    $(document).on('keyup change', '.pricing-opt-min-pax', function() {
        var priceVal = $(this).val();
        $(this).siblings('.pricing-opt-max-pax').attr('min', priceVal);
    });

    if ($('#wp-travel-use-global-tabs').is(':checked')) {
        $('#wp-travel-tab-content-tabs .wp-travel-sorting-tabs').css({ "opacity": "0.3", "pointer-events": "none" });
    } else {
        $('#wp-travel-tab-content-tabs .wp-travel-sorting-tabs').css({ "opacity": "1", "pointer-events": "auto" });
    }
    $('#wp-travel-use-global-tabs').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-tab-content-tabs .wp-travel-sorting-tabs').css({ "opacity": "0.3", "pointer-events": "none" });
        } else {
            $('#wp-travel-tab-content-tabs .wp-travel-sorting-tabs').css({ "opacity": "1", "pointer-events": "auto" });
        }
    });

    if ($('#wp-travel-use-global-trip-enquiry').is(':checked')) {
        $('#wp-travel-enable-trip-enquiry-option-row').addClass( 'hidden' );
    } else {
        $('#wp-travel-enable-trip-enquiry-option-row').removeClass( 'hidden' );
    }

    $('#wp-travel-use-global-trip-enquiry').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-enable-trip-enquiry-option-row').addClass( 'hidden' );
        } else {
            $('#wp-travel-enable-trip-enquiry-option-row').removeClass( 'hidden' );
        }
    });

    // WP Travel Standard Paypal Merged. @since 1.2.1
    // Change max partial payment amount on check / uncheck enable sale.
    $(document).on('click', '#wp-travel-enable-sale', function() {
        var max_payment = $('#wp-travel-price').val();
        if ($(this).is(':checked')) {
            max_payment = $('#wp-travel-sale-price').val();
        }
        // $('#wp-travel-minimum-partial-payout').attr('max', max_payment);
    });

    // Change max partial payment amount on changing sale price.
    $(document).on('change', '#wp-travel-sale-price', function() {
        var max_payment = $(this).val();
        // $('#wp-travel-minimum-partial-payout').attr('max', max_payment);
    });

    $(document).on('click', '#wp-travel-minimum-partial-payout-percent-use-global', function() {
        show_custom_payout_field();
    });
    // Ends WP Travel Standard Paypal Merged. @since 1.2.1

    $('input[type="number"]').on('change', function() {
        if ($(this).attr('placeholder') == 'Min PAX') {
            var minPax = $(this).val();
            $(this).siblings('input[type="number').attr('min', minPax);
        }
    });

    jQuery('.select-main .close').addClass( 'hidden' );
    jQuery(document).on('click', '.select-main .close', function() {
        $(this).siblings('.wp-travel-active').removeClass('wp-travel-active');
        $(this).siblings('.carret').removeClass( 'hidden' );
        $(this).addClass( 'hidden' );

    });
    jQuery(document).on('click', '.select-main, .select-main .caret', function(e) {
        if ($(this).find('ul.wp-travel-active').length == 0) {
            $(this).children('ul').addClass('wp-travel-active');
            $(this).children('.close').removeClass( 'hidden' );
            $(this).children('.carret').addClass( 'hidden' );
        } else {
            $(this).children('.carret').removeClass( 'hidden' );
            $(this).children('.close').addClass( 'hidden' );
            $(this).children('ul').removeClass('wp-travel-active');
        }
    });

    $(document).on("click", function(event) {
        var $trigger = $(".select-main");
        if ($trigger !== event.target && !$trigger.has(event.target).length) {
            $("ul.wp-travel-active").removeClass("wp-travel-active");
            $('.select-main').children('.carret').removeClass( 'hidden' );
            $('.select-main').children('.close').addClass( 'hidden' );
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
    jQuery(document).on('change', '.multiselect-all', function($) {
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


    //Facts.
    jQuery(document).on('click', '.fact-deleter', function() {
        jQuery(this).parent().parent().parent().remove();
    })

    const types = {
        multiple: function(obj, unique) {
            val = this.val();
            index = this.data('index');
            jQuery('.fact-' + index).html((obj.options || []).map(function(option) {
                return jQuery('<label><input type="checkbox" name="wp_travel_trip_facts[' + unique + '][value][]" value="' + option + '"  >' + option + '</label>');
            }));
        },
        single: function(obj, unique) {
            val = this.val();
            index = this.data('index');
            jQuery('.fact-' + index).html(jQuery('<select>').attr('name', 'wp_travel_trip_facts[' + unique + '][value]').html((obj.options || []).map(function(option) {
                return jQuery('<option>').attr('value', option).html(option);
            })));
        },
        text: function(obj, unique) {
            val = this.val();
            index = this.data('index');
            jQuery('.fact-' + index).html(jQuery('<input type="text">').attr('name', 'wp_travel_trip_facts[' + unique + '][value]'));
        },
        default: function(obj, unique) {

            val = this.val();
            index = this.data('index');
            jQuery('.icon-' + index).attr('name', 'wp_travel_trip_facts[' + unique + '][icon]').val(obj.icon);
            jQuery('.type-' + index).attr('name', 'wp_travel_trip_facts[' + unique + '][type]').val(obj.type);

        }
    }

    jQuery(document).on('change', '.fact-type-selecter', function() {
        const unique = Math.random().toString(36).substr(2, 9);
        jQuery(this).attr('name', 'wp_travel_trip_facts[' + unique + '][label]');
        const val = jQuery(this).val();
        var settings = jQuery('#accordion-fact-data').data('factssettings');
        const setting = settings.filter(function(setting) {
            return val == setting['name']
        })[0];
        const type = settings.filter(function(setting) {
            return val == setting['type']
        })[0];
        if (setting) {
            types[setting.type].call(jQuery(this), setting, unique);
            types['default'].call(jQuery(this), setting, unique);
        } else {

        }
    });

}(jQuery));