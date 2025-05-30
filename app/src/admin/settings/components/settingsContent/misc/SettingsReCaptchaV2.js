import { applyFilters, addFilter } from '@wordpress/hooks';
import { Notice } from "@wordpress/components";
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.recaptcha_v2.recaptcha_v2}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More ReCaptcha options according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {_wp_travel.pro_version < 5.4 && _wp_travel.pro_version != null &&
                        <Notice isDismissible={false} status="informational">
                            <strong>{__('Looks like you haven\'t updated your WP Travel Pro plugin.', 'wp-travel')}</strong>
                            <br />
                            {__('Update WP Travel Pro to gain access to additional settings.', 'wp-travel')}
                            <br />
                            <br />
                            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Update WP Travel Pro', 'wp-travel')}</a>
                        </Notice>
                    ||
                    applyFilters('wp_travel_settings_tab_misc_recaptcha_v2', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}

// reCaptcha V2 Checkbox Notice
addFilter('wp_travel_settings_tab_misc_recaptcha_v2', 'WPTravel/Settings/Misc/Notices', (content, allData) => {
    let recaptchaV2Notice = {
        recaptchaV2:
            <>
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Add reCaptcha verification in your site.', 'wp-travel')}</strong>
                    <br />
                    {__('You can display reCaptcha V2 checkbox your site.', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
                </Notice><br />
            </>,
    }
    recaptchaV2Notice = applyFilters('wp_travel_misc_addons_notices', recaptchaV2Notice, allData);
    if (Object.keys(recaptchaV2Notice).length > 0) {
        let notices = Object.keys(recaptchaV2Notice).map((index) => {
            return recaptchaV2Notice[index]
        })
        content = [
            notices
            ,
            ...content,
        ]
    }
    return content
});