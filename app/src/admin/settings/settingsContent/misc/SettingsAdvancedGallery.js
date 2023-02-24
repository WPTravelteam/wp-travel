import { applyFilters, addFilter } from '@wordpress/hooks';
import { Notice } from "@wordpress/components";
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Advanced Gallery", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More advanced gallery according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_misc_advanced_gallery', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}

// Advanced Gallery Notice
addFilter('wp_travel_settings_tab_misc_advanced_gallery', 'WPTravel/Settings/Misc/Notices', (content, allData) => {
    let advancedGalleryNotice = {
        advancedGallery:
            <>
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Change Advanced Gallery in your site.', 'wp-travel')}</strong>
                    <br />
                    {__('You can display advanced gallery in pages or sidebar of your site.', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
                </Notice><br />
            </>,
    }
    advancedGalleryNotice = applyFilters('wp_travel_misc_addons_notices', advancedGalleryNotice, allData);
    if (Object.keys(advancedGalleryNotice).length > 0) {
        let notices = Object.keys(advancedGalleryNotice).map((index) => {
            return advancedGalleryNotice[index]
        })
        content = [
            notices
            ,
            ...content,
        ]
    }
    return content
});