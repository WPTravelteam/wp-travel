jQuery(document).ready(function($) {
    $('.btn-wp-travel-filter').on('click', function() {
        var view_mode = $('.wp-travel-view-mode.active-mode').data('mode');
        var pathname = $('#wp-travel-archive-url').val();
        if (!pathname) {
            pathname = window.location.pathname;
        }
        var query_string = '';
        if ( window.location.search ) {
            query_string = window.location.search;
        }
        var full_url       = new URL( pathname );
        var search_params  = full_url.searchParams;

        $('.wp_travel_input_filters').each(function() {
            var filterby     = $(this).attr('name');
            var filterby_val = $(this).val();

            search_params.set( filterby, filterby_val );
            full_url.search = search_params.toString();
        })

        var new_url     = full_url.toString();
        window.location = new_url;

    });

    // Set view mode class on body on initial load.
    var default_view_mode = $('.wp-travel-view-mode.active-mode').data('mode');
    if ('grid' == default_view_mode) {
        $('body').addClass('wp-travel-grid-mode');
    } else {
        $('body').removeClass('wp-travel-list-mode');
    }

    //New Layout JS

    //customize select option
    var x, i, j, l, ll, selElmnt, a, b, c;
    /*look for any elements with the class "custom-select":*/
    x = document.getElementsByClassName("wti__filter-input");
    l = x.length;
    for (i = 0; i < l; i++) {
        selElmnt = x[i].getElementsByTagName("select")[0];
        ll = selElmnt.length;
        /*for each element, create a new DIV that will act as the selected item:*/
        a = document.createElement("DIV");
        a.setAttribute("class", "select-selected");
        a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
        x[i].appendChild(a);
        /*for each element, create a new DIV that will contain the option list:*/
        b = document.createElement("DIV");
        b.setAttribute("class", "select-items select-hide");
        for (j = 0; j < ll; j++) {
            /*for each option in the original select element,
            create a new DIV that will act as an option item:*/
            c = document.createElement("DIV");
            c.innerHTML = selElmnt.options[j].innerHTML;
            c.addEventListener("click", function(e) {
                /*when an item is clicked, update the original select box,
                and the selected item:*/
                var y, i, k, s, h, sl, yl;
                s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                sl = s.length;
                h = this.parentNode.previousSibling;
                for (i = 0; i < sl; i++) {
                if (s.options[i].innerHTML == this.innerHTML) {
                    s.selectedIndex = i;
                    h.innerHTML = this.innerHTML;
                    y = this.parentNode.getElementsByClassName("same-as-selected");
                    yl = y.length;
                    for (k = 0; k < yl; k++) {
                    y[k].removeAttribute("class");
                    }
                    this.setAttribute("class", "same-as-selected");
                    break;
                }
                }
                h.click();
            });
            b.appendChild(c);
        }
        x[i].appendChild(b);
        a.addEventListener("click", function(e) {
            /*when the select box is clicked, close any other select boxes,
            and open/close the current select box:*/
            e.stopPropagation();
            closeAllSelect(this);
            this.nextSibling.classList.toggle("select-hide");
            this.classList.toggle("select-arrow-active");
        });
    }
    function closeAllSelect(elmnt) {
    /*a function that will close all select boxes in the document,
    except the current select box:*/
    var x, y, i, xl, yl, arrNo = [];
    x = document.getElementsByClassName("select-items");
    y = document.getElementsByClassName("select-selected");
    xl = x.length;
    yl = y.length;
    for (i = 0; i < yl; i++) {
        if (elmnt == y[i]) {
        arrNo.push(i)
        } else {
        y[i].classList.remove("select-arrow-active");
        }
    }
    for (i = 0; i < xl; i++) {
        if (arrNo.indexOf(i)) {
        x[i].classList.add("select-hide");
        }
    }
    }
    /*if the user clicks anywhere outside the select box,
    then close all select boxes:*/
    document.addEventListener("click", closeAllSelect);
    //grid list view filter
    $('.wti__grid-list-filter .wti__button').on('click', function(){
        $(this).addClass('active').siblings('.wti__button').removeClass('active');
        var view_layout = $(this).data('view');
        $('.wti__list-wrapper').removeClass('grid-view list-view');
        $('.wti__list-wrapper').addClass(view_layout);
    });

    $(document).on( 'click', '.btn-wptravel-filter-by', function(){
        var parent = $(this).parent( '.wp-travel-filter-by-heading' );
        if ( parent &&  parent.siblings( '.wp-toolbar-filter-field' ) ) {
            parent.siblings( '.wp-toolbar-filter-field, .wp-travel-filter-button' ).toggleClass( 'show-in-mobile' );

            if ( parent.siblings( '.wp-toolbar-filter-field' ).hasClass( 'show-in-mobile' ) ) {
                $(this).addClass( 'active' );
            } else {
                $(this).removeClass( 'active' );
            }
        }
    } );

});