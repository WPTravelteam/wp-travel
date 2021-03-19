/**
 * =========================
 * detecting sticky
 * =========================
 */
//  jQuery(document).ready(function($) {
    /** Original code is this but due to minification problem of es6 code .. this is converted to es5 code.  starts here */
        // const el = document.querySelector(".wti__nav-tabs")
        // const observer = new IntersectionObserver( 
        // ([e]) => e.target.classList.toggle("sticky-mode", e.intersectionRatio < 1),
        // { threshold: [1] }
        // );
        // observer.observe(el);
    /** End here */
//     function wpTravelInteractionObserver() {
//         function _slicedToArray(arr, i) {
//             return (
//                 _arrayWithHoles(arr) ||
//                 _iterableToArrayLimit(arr, i) ||
//                 _unsupportedIterableToArray(arr, i) ||
//                 _nonIterableRest()
//             );
//         }
//         function _nonIterableRest() {
//             throw new TypeError(
//                 "Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."
//             );
//         }
//         function _unsupportedIterableToArray(o, minLen) {
//             if (!o) return;
//             if (typeof o === "string") return _arrayLikeToArray(o, minLen);
//             var n = Object.prototype.toString.call(o).slice(8, -1);
//             if (n === "Object" && o.constructor) n = o.constructor.name;
//             if (n === "Map" || n === "Set") return Array.from(o);
//             if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))
//                 return _arrayLikeToArray(o, minLen);
//         }
//         function _arrayLikeToArray(arr, len) {
//             if (len == null || len > arr.length) len = arr.length;
//             for (var i = 0, arr2 = new Array(len); i < len; i++) {
//                 arr2[i] = arr[i];
//             }
//             return arr2;
//         }
//         function _iterableToArrayLimit(arr, i) {
//             if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr)))
//                 return;
//             var _arr = [];
//             var _n = true;
//             var _d = false;
//             var _e = undefined;
//             try {
//                 for (
//                     var _i = arr[Symbol.iterator](), _s;
//                     !(_n = (_s = _i.next()).done);
//                     _n = true
//                 ) {
//                     _arr.push(_s.value);
//                     if (i && _arr.length === i) break;
//                 }
//             } catch (err) {
//                 _d = true;
//                 _e = err;
//             } finally {
//                 try {
//                     if (!_n && _i["return"] != null) _i["return"]();
//                 } finally {
//                     if (_d) throw _e;
//                 }
//             }
//             return _arr;
//         }
//         function _arrayWithHoles(arr) {
//             if (Array.isArray(arr)) return arr;
//         }
//         var el = document.querySelector(".wti__nav-tabs");
//         var observer = new IntersectionObserver(
//             function (_ref) {
//                 var _ref2 = _slicedToArray(_ref, 1),
//                     e = _ref2[0];
//                 return e.target.classList.toggle("sticky-mode", e.intersectionRatio < 1);
//             },
//             {
//                 threshold: [1]
//             }
//         );
//         observer.observe(el);
//     }
//     wpTravelInteractionObserver();
// });
/**
 * =========================
 * detecting sticky end
 * =========================
 */
jQuery(function ($) {

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

    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character

        if ($("ul.resp-tabs-list > li." + hash).hasClass('wp-travel-ert')) {
            lis = $("ul.resp-tabs-list > li");
            lis.removeClass("resp-tab-active");
            $("ul.resp-tabs-list > li." + hash).addClass("resp-tab-active");
            tab_cont = $('.tab-list-content');
            tab_cont.removeClass('resp-tab-content-active').hide();
            $('#' + hash + '.tab-list-content').addClass('resp-tab-content-active').show();
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

    // Rating script starts.
    $('.rate_label').hover(function () {
        var rateLabel = $(this).attr('data-id');
        $('.rate_label').removeClass('fas');

        rate(rateLabel);
    },
        function () {
            var ratedLabel = $('#wp_travel_rate_val').val();

            $('.rate_label').removeClass('fas').addClass('far');
            if (ratedLabel > 0) {
                rate(ratedLabel);
            }
        });

    function rate(rateLabel) {
        for (var i = 0; i < rateLabel; i++) {
            $('.rate_label:eq( ' + i + ' )').addClass('fas').removeClass('far');
        }

        for (j = 4; j >= i; j--) {
            $('.rate_label:eq( ' + j + ' )').addClass('far');
        }
    }

    // click
    $('.rate_label').click(function (e) {
        e.preventDefault();
        $('#wp_travel_rate_val').val($(this).attr('data-id'));
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

});