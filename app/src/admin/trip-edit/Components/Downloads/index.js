import { Notice } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
const __i18n = {
	..._wp_travel_admin.strings
}


// Single Components for hook callbacks.
const DownloadsNotice = ( {settingsData, map_data } ) => {
    return <>
        <Notice isDismissible={false} status="informational">
            <strong>{__i18n.notices.downloads_option.title}</strong>
            <br />
            {__i18n.notices.downloads_option.description}
            <br />
            <br />
            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
        </Notice><br />
    </>
}

// Callbacks.
const DownloadsNoticeCB = ( content, allData ) => {
    return [ ...content, <DownloadsNotice allData={allData} key="DownloadsNotice" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_downloads', 'WPTravel/TripEdit/DownloadsNotice', DownloadsNoticeCB, 10 );
// wptravel_trip_edit_tab_content_downloads

addFilter( 'wptravel_trip_edit_block_tab_downloads', 'WPTravel/TripEdit/Block/Downloads/DownloadsFields', DownloadsNoticeCB );
