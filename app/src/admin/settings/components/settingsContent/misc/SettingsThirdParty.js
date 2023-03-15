import { applyFilters, addFilter, removeFilter } from '@wordpress/hooks';
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
                    {__("Third Party Integrations", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More third party supports according to your choice.", "wp-travel")}
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
                    }
                    {applyFilters('wp_travel_settings_tab_misc_fixer_api', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_currency_exchange', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_google_calendar', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_trip_weather_forecast', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_wishlists', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_zapier', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_multiple_currency', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_mailchimp', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}


// Currency Exchange Notice
addFilter('wp_travel_settings_tab_misc_currency_exchange', 'WPTravel/Settings/Misc/Notices', (content, allData) => {
    let currencyExchangeNotice = {
        currencyExchange:
            <>
                <h3>{__('Currency Exchange Rate API', 'wp-travel')}</h3>
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Display current exchange rate in your site.', 'wp-travel')}</strong>
                    <br />
                    {__('You can display current exchange rate for different currency in pages or sidebar of your site. Checkout out', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
                </Notice><br />
            </>,
    }
    currencyExchangeNotice = applyFilters('wp_travel_misc_addons_notices', currencyExchangeNotice, allData);
    if (Object.keys(currencyExchangeNotice).length > 0) {
        let notices = Object.keys(currencyExchangeNotice).map((index) => {
            return currencyExchangeNotice[index]
        })
        content = [
            notices
            ,
            ...content,
        ]
    }
    return content
});

// Mailchimp Notice
addFilter('wp_travel_settings_tab_misc_mailchimp', 'WPTravel/Settings/Misc/Notices', (content, allData) => {
    let mailchimpNotice = {
        mailchimp: <>
            <h3>{__('Mailchimp Settings', 'wp-travel')}</h3>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Using Mailchimp for email marketing?', 'wp-travel')}</strong>
                <br />
                {__('You can import customer email from booking and inquiry to Mailchimp. That help you grow your business.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
    }
    mailchimpNotice = applyFilters('wp_travel_misc_addons_notices', mailchimpNotice, allData);
    if (Object.keys(mailchimpNotice).length > 0) {
        let notices = Object.keys(mailchimpNotice).map((index) => {
            return mailchimpNotice[index]
        })
        content = [
            notices
            ,
            ...content,
        ]
    }
    return content
});

// Wishlists Notice
addFilter('wp_travel_settings_tab_misc_wishlists', 'WPTravel/Settings/Misc/Notices', (content, allData) => {
    let wishlistsNotice = {
        wishlists: <>
            <h3>{__('Wishlists Options', 'wp-travel')}</h3>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Allow customers to save trip for future.', 'wp-travel')}</strong>
                <br />
                {__('Whishlists helps user to save trip they like for future, so that they can book them later. ', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
    }
    wishlistsNotice = applyFilters('wp_travel_misc_addons_notices', wishlistsNotice, allData);
    if (Object.keys(wishlistsNotice).length > 0) {
        let notices = Object.keys(wishlistsNotice).map((index) => {
            return wishlistsNotice[index]
        })
        content = [
            notices
            ,
            ...content,
        ]
    }
    return content
});