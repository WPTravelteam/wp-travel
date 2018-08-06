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

        var prices = trip_prices_data.prices.map(function(x) {
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
        $(".wp-travel-range-slider").slider({
            range: true,
            min: min,
            max: max,
            values: [filteredMin, filteredMax],
            slide: function(event, ui) {
                $(".price-amount").val(trip_prices_data.currency_symbol + ui.values[0] + " - " + trip_prices_data.currency_symbol + ui.values[1]);
                $('.wp-travel-range-slider').siblings('.wp-travel-filter-price-min').val(ui.values[0]);
                $('.wp-travel-range-slider').siblings('.wp-travel-filter-price-max').val(ui.values[1]);
            }
        });
        $(".price-amount").val(trip_prices_data.currency_symbol + $(".wp-travel-range-slider").slider("values", 0) +
            " - " + trip_prices_data.currency_symbol + $(".wp-travel-range-slider").slider("values", 1));

        $(".trip-duration-calender input").datepicker({
            language: trip_prices_data.locale,
        });

    });

    $('.wp-travel-filter-search-submit').on('click', function() {
        var view_mode = $(this).siblings('.wp-travel-widget-filter-view-mode').data('mode');
        pathname = $(this).siblings('.wp-travel-widget-filter-archive-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }
        query_string = '?';
        var check_query_string = pathname.match(/\?/);
        if (check_query_string) {
            query_string = '&';
        }
        var data_index = $(this).siblings('.filter-data-index').data('index');
        $('.wp_travel_search_widget_filters_input' + data_index).each(function() {
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