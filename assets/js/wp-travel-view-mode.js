jQuery(document).ready(function($) {
    $('.wp_travel_input_filters').on('change', function() {
        var view_mode = $('.wp-travel-view-mode.active-mode').data('mode');

        pathname = window.location.pathname;
        query_string = '?';
        $('.wp_travel_input_filters').each(function() {
            filterby = $(this).attr('name');
            filterby_val = $(this).val();
            query_string += filterby + '=' + filterby_val + '&';
        })
        redirect_url = pathname + query_string;
        redirect_url = redirect_url.replace(/&+$/, '');

        redirect_url = redirect_url + '&view_mode=' + view_mode;
        window.location = redirect_url;
    });

    // Set view mode class on body on initial load.
    var default_view_mode = $('.wp-travel-view-mode.active-mode').data('mode');
    if ('grid' == default_view_mode) {
        $('body').addClass('wp-travel-grid-mode');
    } else {
        $('body').removeClass('wp-travel-list-mode');
    }

});