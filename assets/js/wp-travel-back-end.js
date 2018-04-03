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
                    map.setCenter(wp_travel_drag_drop_uploader.lat, wp_travel_drag_drop_uploader.lng);
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

        if ($.fn.datepicker) {
            $('#wp-travel-start-date').datepicker({
                language: 'en',
                minDate: new Date(),
                onSelect: function(dateStr) {
                    newMinDate = null;
                    newMaxDate = new Date();
                    if ('' !== dateStr) {
                        new_date_min = new Date(dateStr);

                        newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate())));
                    }
                    $('#wp-travel-end-date').datepicker({
                        minDate: newMinDate,
                    });
                }
            });

            $('#wp-travel-end-date').datepicker({
                language: 'en',
                minDate: new Date()
            });

            $('.wp-travel-datepicker').datepicker({
                language: 'en',
                minDate: new Date()
            });

            $('.wp-travel-timepicker').datepicker({
                // language: 'en',
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
            if ('' == end_date) {
                error += 'End date can\'t be empty!' + "\n";
            }

            if ('' != start_date && '' != end_date) {
                start_date = new Date(start_date);
                end_date = new Date(end_date);

                if (end_date <= start_date) {
                    error += 'End date must greater than start date.' + "\n";
                }
            }

        }

        if ('' == error) {
            $(document).off('click', '#publish');
        } else {
            alert(error);
            return false;
        }
    });

    $(document).on('click', '#wp-travel-enable-sale', function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-sale-price').removeAttr('disabled').closest('tr').show();
            $('#wp-travel-price').attr('required', 'required');
        } else {
            $('#wp-travel-sale-price').attr('disabled', 'disabled').closest('tr').hide();
            $('#wp-travel-price').removeAttr('required');
        }
    });

    // Slugify the text string.
    function wp_travel_slugify_string(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/[^\w\-]+/g, '') // Remove all non-word chars
            .replace(/\-\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
    }

    // Pricing options enable disable. v 1.2.5
    $(document).on('click', '#wp-travel-enable-pricing-options', function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-multiple-pricing-options').show();
        } else {
            $('#wp-travel-multiple-pricing-options').hide();
        }
    });

    //Pricing Key slugify.
    $(document).on('change', '.wp-travel-variation-pricing-name', function() {

        var price_key = wp_travel_slugify_string($(this).val());

        $(this).siblings('.wp-travel-variation-pricing-uniquekey').val(price_key)

    });

    // Pricing options change function.
    $(document).on('change', '.wp-travel-pricing-options-list', function() {
        if ($(this).val() === 'custom') {
            $(this).parents('.repeat-row').next('.custom-pricing-label-wrap').show();
        } else {
            $(this).parents('.repeat-row').next('.custom-pricing-label-wrap').hide();
        }
    });

    //Pricing options Enable Sale.
    $(document).on('change', '.wp-travel-enable-variation-price-sale', function() {
        if ($(this).is(':checked')) {
            $(this).parents('.repeat-row').next('.repeat-row').show();
        } else {
            $(this).parents('.repeat-row').next('.repeat-row').hide();
        }
    });

    if ($('.wp-travel-enable-variation-price-sale').is(':checked')) {
        $(this).parents('.repeat-row').next('.repeat-row').show();
    } else {
        $(this).parents('.repeat-row').next('.repeat-row').hide();
    }

    if ($('#wp-travel-enable-sale').is(':checked')) {
        $('#wp-travel-price').attr('required', 'required');
    }

    $(document).on('click', '#wp-travel-fixed-departure', function() {
        if ($(this).is(':checked')) {
            $('.wp-travel-fixed-departure-row').css({ 'display': 'table-row' });
            $('.wp-travel-trip-duration-row').css({ 'display': 'none' });
        } else {
            $('.wp-travel-fixed-departure-row').css({ 'display': 'none' });
            $('.wp-travel-trip-duration-row').css({ 'display': 'table-row' });
        }
    });

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
        var wp_travel_rand_integer = Math.floor(Math.random() * 100000) + 1;
        var wp_travel_itinerary_id = 'wp_travel_itinerary_data_' + wp_travel_rand_integer;
        // var wp_travel_editor_settings = tinyMCEPreInit.mceInit.content;
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                'action': 'wp_travel_add_itinerary_content_data',
                'default_text': 'Add Itinerary Description',
                'itinerary_id': wp_travel_itinerary_id
            },
            success: function(response) {
                $('.itinerary_block').append(response);
                // tinyMCE.execCommand('mceAddEditor', true, wp_travel_itinerary_id);
                // quicktags({ id: wp_travel_itinerary_id });
                $('.itinerary_block .panel:last .wp-travel-datepicker').datepicker({
                    language: 'en',
                    minDate: new Date()
                });
                $('.itinerary_block .panel:last .wp-travel-timepicker').datepicker({
                    language: 'en',
                    timepicker: true,
                    onlyTimepicker: true,

                });
                return false;
            }
        });

        // function get_tinymce_content(id) {
        //     var content;
        //     var inputid = id;
        //     var editor = tinyMCE.get(inputid);
        //     var textArea = $('textarea#' + inputid);
        //     if (textArea.length > 0 && textArea.is(':visible')) {
        //         content = textArea.val();
        //     } else {
        //         content = editor.getContent();
        //     }
        //     return content;
        // }

    });
    //Remove Itinerary Data Row.

    $(document).on('click', '.remove_itinery', function(e) {
        e.preventDefault();
        $(this).closest('.itinerary_wrap').remove();
        return false;
    });
    var textareaID;
    $('#tab-accordion .wp-travel-sorting-tabs,#tab-accordion-itineraries #accordion-itinerary-data').sortable({
        handle: '.wp-travel-sorting-handle',
        // start: function(event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
        //     textareaID = $(ui.item).find('.wp-editor-container textarea').attr('id');
        //     try { tinyMCE.execCommand('mceRemoveEditor', false, textareaID); } catch (e) {}
        // },
        // stop: function(event, ui) { // re-initialize TinyMCE when sort is completed
        //     try { tinyMCE.execCommand('mceAddEditor', false, textareaID); } catch (e) {}
        //     $(this).find('.update-warning').show();
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
    // Open All And Close All accordion.
    $('.open-all-link').click(function(e) {
        e.preventDefault();
        var parent = '#' + $(this).data('parent');
        $(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
        $(parent + ' .panel-collapse').addClass('in');
        $(this).hide();
        $(parent + ' .close-all-link').show();
        $(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
    });
    $('.close-all-link').click(function(e) {
        var parent = '#' + $(this).data('parent');
        e.preventDefault();
        $(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
        $(parent + ' .panel-collapse').removeClass('in');
        $(this).hide();
        $(parent + ' .open-all-link').show();
    });


    $('.ui-accordion-header').click(function() {
        $('.open-all-link').show();
        $('.close-all-link').show();
    });

    $(document).on('click', '.wt-accordion-close', function(e) {
        var acc_id = $(this).closest('.tab-accordion').attr('id');
        if (confirm("Are you sure to Delete ?") == true) {
            $(this).closest('div.panel-default').remove();

            var faqs = $('#' + acc_id + ' .panel-default').length;

            // alert(faqs);
            if (faqs > 0) {
                $('.while-empty').hide();
                $('.wp-collapse-open').show();
            } else {
                $('.wp-collapse-open').hide();
                $('.while-empty').show();
            }
        }
        return;
    });

    $('.wp-travel-faq-add-new').on('click', function() {
        var template = wp.template('wp-travel-faq');
        var faqs = $('#accordion-faq-data panel-default').length;
        var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
        $('#accordion-faq-data').append(template({ random: rand }));

        $('.while-empty').hide();
        $('.wp-collapse-open').show();
        // $('#tab-accordion').accordion('destroy').accordion({ active: faqs });
    });

    // Pricing options template.
    $('.wp-travel-pricing-add-new').on('click', function() {
        var template = wp.template('wp-travel-pricing-options');
        var rand = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        $('#pricing-options-data').append(template({ random: rand }));


    });

    //value bind to label.
    $(document).on('change keyup', "*[bind]", function(e) {
        var to_bind = $(this).attr('bind');
        var value = ('' != $(this).val()) ? $(this).val() : 'Untitled';
        $("*[bind='" + to_bind + "']").html(value);
        $("*[bind='" + to_bind + "']").val($(this).val());
    });

    //Sale price binding
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

    if ($('#wp-travel-use-global-tabs').is(':checked')) {
        $('#wp-travel-tab-content-setting .wp-travel-sorting-tabs').css({ "opacity": "0.3", "pointer-events": "none" });
    } else {
        $('#wp-travel-tab-content-setting .wp-travel-sorting-tabs').css({ "opacity": "1", "pointer-events": "auto" });
    }
    $('#wp-travel-use-global-tabs').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-tab-content-setting .wp-travel-sorting-tabs').css({ "opacity": "0.3", "pointer-events": "none" });
        } else {
            $('#wp-travel-tab-content-setting .wp-travel-sorting-tabs').css({ "opacity": "1", "pointer-events": "auto" });
        }
    });

    if ($('#wp-travel-use-global-trip-enquiry').is(':checked')) {
        $('#wp-travel-enable-trip-enquiry-option-row').hide();
    } else {
        $('#wp-travel-enable-trip-enquiry-option-row').show();
    }

    $('#wp-travel-use-global-trip-enquiry').change(function() {
        if ($(this).is(':checked')) {
            $('#wp-travel-enable-trip-enquiry-option-row').hide();
        } else {
            $('#wp-travel-enable-trip-enquiry-option-row').show();
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
        if ($(this).is(':checked')) {
            $('#wp-travel-minimum-partial-payout-percent').attr('disabled', 'disabled').closest('tr').hide();
        } else {
            $('#wp-travel-minimum-partial-payout-percent').removeAttr('disabled', 'disabled').closest('tr').show();
        }
    });
    // Ends WP Travel Standard Paypal Merged. @since 1.2.1


}(jQuery));