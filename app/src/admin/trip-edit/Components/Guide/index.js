import { Notice } from '@wordpress/components';
import { addFilter, applyFilters, removeAllFilters } from '@wordpress/hooks';
const __i18n = {
    ..._wp_travel_admin.strings
}


// Single Components for hook callbacks.
const GuideNotice = ( {settingsData, map_data } ) => {
    return <>
        <Notice isDismissible={false} status="informational">
            <strong>{__i18n.notices.checkout_option.title}</strong>
            <br />
            {__i18n.notices.checkout_option.description}
            <br />
            <br />
            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
        </Notice><br />
    </>
}

// Callbacks.
const GuideNoticeCB = ( content, allData ) => {
    return [ ...content, <GuideNotice allData={allData} key="GuideNotice" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_guides', 'WPTravel/TripEdit/GuideNotice', GuideNoticeCB, 10 );

