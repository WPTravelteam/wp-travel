// var MONTHS = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var config = {
    type: 'line',
    data: {
        labels: JSON.parse(wp_travel_chart_data.labels),
        datasets: JSON.parse(wp_travel_chart_data.datasets)
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: wp_travel_chart_data.chart_title
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: false,
                    labelString: 'Year'
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: false,
                    labelString: 'Value'
                }
            }]
        }
    }
};

window.onload = function() {
    var ctx = document.getElementById("wp-travel-booking-canvas").getContext("2d");
    window.myLine = new Chart(ctx, config);
};

jQuery(document).ready(function($) {

    $('.wp-travel-max-bookings').html(wp_travel_chart_data.max_bookings);
    $('.wp-travel-max-pax').html(wp_travel_chart_data.max_pax);
    $('.wp-travel-top-countries').html(wp_travel_chart_data.top_countries);

    var edit_url = 'javascript:void(0)';
    if (wp_travel_chart_data.top_itinerary.id) {
        edit_url = 'post.php?post=' + wp_travel_chart_data.top_itinerary.id + '&action=edit';
    }
    $('.wp-travel-top-itineraries').attr('href', edit_url).html(wp_travel_chart_data.top_itinerary.name);

    $('#datepicker-from').datepicker({
        language: 'en',
        maxDate: new Date(),
        onSelect: function(dateStr) {
            newMinDate = null;
            newMaxDate = new Date();
            if ('' !== dateStr) {
                new_date_min = new Date(dateStr);
                new_date_max = new Date(dateStr);

                newMinDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate() + 1)));

                maxDate = new Date(new_date_max.setMonth(new Date(new_date_max.getMonth() + 1)));
                if (maxDate < newMaxDate) {
                    newMaxDate = maxDate;
                }
            }
            $('#datepicker-to').datepicker({
                minDate: newMinDate,
                maxDate: newMaxDate,
            });
        }
    });

    $('#datepicker-to').datepicker({
        language: 'en',
        maxDate: new Date(),
        onSelect: function(dateStr) {
            newMinDate = new Date();
            newMaxDate = null;
            if ('' !== dateStr) {
                new_date_min = new Date(dateStr);
                new_date_max = new Date(dateStr);

                newMinDate = new Date(new_date_max.setMonth(new Date(new_date_max.getMonth() - 1)));
                newMaxDate = new Date(new_date_min.setDate(new Date(new_date_min.getDate() - 1)));

            }
            $('#datepicker-from').datepicker({
                minDate: newMinDate,
                maxDate: newMaxDate,
            });
        }

    });
})