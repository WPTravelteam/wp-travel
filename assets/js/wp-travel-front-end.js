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

        // if (hash === 'tab-7') {
        var winWidth = $(window).width();
        var tabHeight = $('.wp-travel-tab-wrapper').offset().top;
        if (winWidth < 767) {
            var tabHeight = $('.resp-accordion.resp-tab-active').offset().top;
        }
        $('html, body').animate({
            scrollTop: (tabHeight)
        }, 1200);
        // }
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

});