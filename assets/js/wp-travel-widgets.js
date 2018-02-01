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

        var prices = trip_prices_data.map(function(x) {
            return parseInt(x, 10);
        });

        var min = Math.min.apply(null, prices),
            max = Math.max.apply(null, prices)

        if (findGetParameter('min_price')) {
            var filteredMin = findGetParameter('min_price');
        } else {
            filteredMin = min;
        }
        if (findGetParameter('max_price')) {
            var filteredMax = findGetParameter('max_price');
        } else {
            filteredMax = max;
        }

        // Filter Range Slider Widget.
        $("#slider-range").slider({
            range: true,
            min: min,
            max: max,
            values: [filteredMin, filteredMax],
            slide: function(event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                $('#wp-travel-filter-price-min').val(ui.values[0]);
                $('#wp-travel-filter-price-max').val(ui.values[1]);
            }
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) +
            " - $" + $("#slider-range").slider("values", 1));

        $(".trip-duration-calender input").datepicker({
            language: 'en',
        });

    });

    $('#wp-travel-filter-search-submit').on('click', function() {
        var view_mode = $('#wp-travel-widget-filter-view-mode').data('mode');
        pathname = $('#wp-travel-widget-filter-archive-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }
        query_string = '?';
        var check_query_string = pathname.match(/\?/);
        if (check_query_string) {
            query_string = '&';
        }
        $('.wp_travel_search_widget_filters_input').each(function() {
            filterby = $(this).attr('name');
            filterby_val = $(this).val();
            query_string += filterby + '=' + filterby_val + '&';
        })
        redirect_url = pathname + query_string;
        redirect_url = redirect_url.replace(/&+$/, '');

        redirect_url = redirect_url + '&view_mode=' + view_mode;
        window.location = redirect_url;
    });

});