jQuery(document).ready(function ($) {

	// $('input[name=wp_travel_book_now]').removeAttr('disabled');
	// $('#wp-travel-book-now').click(function () {
	// 	$(this).slideUp('slow').siblings('form').slideToggle('slow');
	// });
	$(window).on('beforeunload', function () {
		$("input[name=wp_travel_book_now]").prop("disabled", "disabled");
	});

	$('.wp-travel-booking-reset').click(function () {
		$(this).closest('form').slideUp('slow').siblings('.wp-travel-book-now').slideToggle('slow');
	});

	$(document).on('click', '.wp-travel-booknow-btn, div.wp-travel-booknow-btn a', function () {
		$(".wp-travel-booking-form").trigger("click");
		var winWidth = $(window).width();
		var tabHeight = $('.wp-travel-tab-wrapper').offset().top;
		if (winWidth < 767) {
			var tabHeight = $('.resp-accordion.resp-tab-active').offset().top;
		}
		$('html, body').animate({
			scrollTop: (tabHeight)
		}, 1200);

	});
	$(document).on('click', '.components-checkbox-control__input', function () {
		var winWidth = $(window).width();
		if (winWidth < 991) {
			var top = jQuery('#wp-travel-booking-recurring-dates').offset().top
			jQuery('html, body').animate({
				scrollTop: ( top - 20 )
			}, 1200);
		}
	});
	$( "input[name=wp_travel_payment_gateway]").click(function () {
		inventory_testing();
	})

});

function sidebarSticky() {
	setInterval(function () {
		jQuery('.container .sticky-sidebar').hcSticky({
			stickTo: jQuery('.wp-travel-minicart'),
			top:50
			});
	}, 1000)
}
jQuery(document).ready(sidebarSticky);
jQuery(window).resize(sidebarSticky);
// document.addEventListener("DOMContentLoaded", function() {
// 	sidebarSticky();
// });


//fixing pre tag issue while using shortcode
// $('.single-itineraries #trip_outline pre').each(function() {
//     document.write("hello");
// 	$(this).replaceWith($('<div>' + this.innerHTML + '</div>'));
//   });

var inventory_testing = function () {
    if ( typeof wp_travel.inventory != 'undefined' && wp_travel.inventory == 'yes' ) {
        fetch(wp_travel.ajaxUrl + "?action=inventory_testing&_nonce=${_wp_travel._nonce}", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
            wp_travel_trip:
                (typeof wp_travel.items != "undefined" && wp_travel.items) || Array(),
            nonce: wp_travel.nonce,
            }),
        }).then(function (resp) {
            resp.json().then((responces) => {
                if (responces.success == true) {
                    if (
                        typeof responces.data != "undefined" &&
                        typeof responces.data.code != "undefined" &&
                        typeof responces.data.inventory_available != "undefined" &&
                        responces.data.code == "WP_TRAVEL_INVENTORY_TESTING" &&
                        responces.data.inventory_available == "no_pax"
                    ) {
                        alert( "Sorry your booking can't proceed, booking is full." )
                        fetch(
                            wp_travel.ajaxUrl + "?action=wp_travel_use_inventory_empty_cart",
                            {
                                method: "POST",
                                headers: { "Content-Type": "application/json" },
                                body: JSON.stringify({
                                wp_travel_empty_cart:
                                    (typeof wp_travel.items != "undefined" && wp_travel.items) ||
                                    Array(),
                                }),
                            }
                            ).then((results) =>
                            results.json().then((resu) => {
                                location.reload();
                            })
                        );
                    } 
                } 
            });
        });
    }
}