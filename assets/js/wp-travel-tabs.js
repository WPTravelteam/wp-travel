(function($) {
    if ($.fn.tabs) {
        $('.wp-travel-tabs-wrap').tabs({
            activate: function(event, ui) {
                $(ui.newPanel).css({ display: 'table' });
                $('#wp-travel-settings-current-tab').val($(ui.newPanel).attr('id'));
            },
            create: function(event, ui) {
                $(ui.panel).css({ display: 'table' });
                $('#wp-travel-settings-current-tab').val($(ui.panel).attr('id'));
            },
            load: function(event, ui) {
                // console.log( ui );
            }
        });

        $(".wp-travel-marketplace-tab-wrap").tabs();


    }

    // Sortable for Global tabs.
    $('#wp-travel-tab-content-tabs_global .wp-travel-sorting-tabs tbody').sortable({
        handle: '.wp-travel-sorting-handle'
    });

    $(document).on('keyup change', '.section_title', function() {
        var title = $(this).val();
        // alert(title);
        $(this).siblings('.wp-travel-accordion-title').html(title);
    });


}(jQuery));