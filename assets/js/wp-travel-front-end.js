jQuery(document).ready(function($) {

    function wp_travel_set_equal_height() {
        var base_height = 0;
        $('.wp-travel-feature-slide-content').css({ 'height': 'auto' });
        var winWidth = window.innerWidth;
        if (winWidth > 992) {

            $('.wp-travel-feature-slide-content').each(function() {
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
        midClick: true
    });

    $('#wp-travel-tab-wrapper').easyResponsiveTabs({

    });

    if (window.location.hash) {
        var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character

        // var match = hash.match(/wp-travel-/);
        // if (!match) {
        //     hash = 'wp-travel-' + hash;
        // }

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

    // $('ul.resp-tabs-list > li').on('click', function() {
    //     // window.location.hash = '';
    //     // history.pushState("", document.title, window.location.pathname);
    // });

    // Rating script starts.
    $('.rate_label').hover(function() {
            var rateLabel = $(this).attr('data-id');
            $('.rate_label').removeClass('dashicons-star-filled');

            rate(rateLabel);
        },
        function() {
            var ratedLabel = $('#wp_travel_rate_val').val();

            $('.rate_label').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
            if (ratedLabel > 0) {
                rate(ratedLabel);
            }
        });

    function rate(rateLabel) {
        for (var i = 0; i < rateLabel; i++) {
            $('.rate_label:eq( ' + i + ' )').addClass('dashicons-star-filled').removeClass('dashicons-star-empty ');
        }

        for (j = 4; j >= i; j--) {
            $('.rate_label:eq( ' + j + ' )').addClass('dashicons-star-empty');
        }
    }

    // click
    $('.rate_label').click(function(e) {
        e.preventDefault();
        $('#wp_travel_rate_val').val($(this).attr('data-id'));
    });
    // Rating script ends.
    //

    $(document).on('click', '.wp-travel-count-info', function(e) {
        e.preventDefault();
        $(".wp-travel-review").trigger("click");
    });

    $(document).on('click', '.top-view-gallery', function(e) {
        e.preventDefault();
        $(".wp-travel-tab-gallery-contnet").trigger("click");
    });

    $(document).on('click', '.wp-travel-count-info, .top-view-gallery', function(e) {
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
    // $(window).on("scroll", function(e) {

    //     var tabWrapper = $('.wp-travel-tab-wrapper');
    //     var tabMenu = $('ul.wp-travel.tab-list');

    //     var tabWrapperTopOffset = tabWrapper.offset().top;
    //     var tabWrapperHeight = tabWrapper.height();
    //     var tabMenuHeight = tabMenu.height();

    //     var winScrollTop = $(window).scrollTop();
    //     if (winScrollTop < (tabWrapperTopOffset + tabWrapperHeight) && winScrollTop >= tabWrapperTopOffset) {
    //         tabMenu.addClass('custom-container fixed');
    //         tabWrapper.css({ 'padding-top': tabMenuHeight });
    //     } else {
    //         tabMenu.removeClass('custom-container fixed');
    //         tabWrapper.css({ 'padding-top': 0 });
    //     }

    // });
    $(window).on("resize", function(e) {
        wp_travel_set_equal_height();
    });

    // Open All And Close All accordion.
    $('.open-all-link').click(function(e) {
        e.preventDefault();
        $('.panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
        $('.panel-collapse').addClass('in');
        // $(this).hide();
        $('.close-all-link').show();
        $('.panel-collapse').css('height', 'auto');
    });
    $('.close-all-link').click(function(e) {
        e.preventDefault();
        $('.panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
        $('.panel-collapse').removeClass('in');
        // $(this).hide();
        $('.open-all-link').show();
    });

    // Enquiry Submission.

    $('#wp-travel-enquiries').submit(function(e) {

        e.preventDefault();

        //Remove any previous errors.
        $('.enquiry-response').remove();

        var formData = {
            'wp_travel_enquiry_name': $('#wp-travel-enquiry-name').val(),
            'wp_travel_enquiry_email': $('#wp-travel-enquiry-email').val(),
            'wp_travel_enquiry_query': $('#wp-travel-enquiry-query').val(),
            'action': 'wp_travel_save_user_enquiry',
            'nonce': wp_travel_frontend_vars.nonce,
            'post_id': $('#wp-travel-enquiry-post-id').val(),
        };
        var text_processing = $('#wp_travel_label_processing').val();
        var text_submit_enquiry = $('#wp_travel_label_submit_enquiry').val();
        $.ajax({
            type: "POST",
            url: wp_travel_frontend_vars.ajaxUrl,
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
                            jQuery('#wp-travel-send-enquiries').magnificPopup('close')
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

    });

    jQuery('.wp-travel-booking-row').hide();
    jQuery('.show-booking-row').click(function(event) {
        event.preventDefault();
        jQuery(this).parent('.action').siblings('.wp-travel-booking-row').toggle('fast').addClass('animate');
        jQuery(this).text(function(i, text) {
            return text === wp_travel_frontend_vars.text_array.pricing_select ? wp_travel_frontend_vars.text_array.pricing_close : wp_travel_frontend_vars.text_array.pricing_select;
        })
    });

    jQuery('.wp-travel-pricing-dates').each(function() {
        var availabledate = jQuery(this).data('available-dates');
        if (availabledate) {
            jQuery(this).datepicker({
                language: wp_travel_frontend_vars.locale,
                // inline: true,
                autoClose: true,
                minDate: new Date(),
                onRenderCell: function(date, cellType) {
                    if (cellType == 'day') {
                        availabledate = availabledate.map(function(d) {
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
            jQuery(this).datepicker({
                language: wp_travel_frontend_vars.locale,
                // inline: true,
                minDate: new Date(),
                autoClose: true,
            });
        }

    })

    $(document).ready(function($) {

        if (typeof parsley == "function") {

            $('input').parsley();

        }

        // $('.add-to-cart-btn').click(function(event) {
        //     event.preventDefault();
        //     // Validate all input fields.
        //     var isValid = true;
        //     var parent = '#' + $(this).data('parent-id');
        //     console.log(parent);
        //     $(parent + ' input').each(function() {
        //         if ($(this).parsley().validate() !== true) isValid = false;
        //     });
        //     if (isValid) {
        //         pathname = $(this).attr('href');
        //         query_string = '?';
        //         var check_query_string = pathname.match(/\?/);
        //         if (check_query_string) {
        //             query_string = '&';
        //         }
        //         $(parent + ' input').each(function() {
        //             filterby = $(this).attr('name');
        //             filterby_val = $(this).val();
        //             query_string += filterby + '=' + filterby_val + '&';
        //         })
        //         redirect_url = pathname + query_string;
        //         redirect_url = redirect_url.replace(/&+$/, '');
        //         location.href = redirect_url;
        //     }
        // });

    });

    jQuery(document).ready(function($) {
        $('.login-page .message a').click(function(e) {
            e.preventDefault();
            $('.login-page form.login-form,.login-page form.register-form').animate({ height: "toggle", opacity: "toggle" }, "slow");
        });
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

    $('.dashtab-nav').click(function(e) {

        e.preventDefault();
        var tab = $(this).data('tabtitle');

        $('#' + tab).click();

    });

    $('#wp-travel-dsh-change-pass-switch').change(function(e) {

        $('#wp-travel-dsh-change-pass').slideToggle();

    });

});