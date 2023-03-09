import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { Notice } from '@wordpress/components';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    return <>
        <div className="wp-travel-section-header">
            <h2 className="wp-travel-section-header-title">
                {__("License", "wp-travel")}
            </h2>
            <p className="wp-travel-section-header-description">
                {__("Get additional features by upgrading to WP Travel Pro.", "wp-travel")}
            </p>
        </div>
        <div className='wp-travel-section-content'>
            <ErrorBoundary>
                <div className="wp-travel-license-details">
                    {applyFilters('wp_travel_license_tab_fields', [], allData)}
                </div>
            </ErrorBoundary>
        </div>
    </>
}

addFilter('wp_travel_license_tab_fields', 'wp_travel', (content, allData) => {

    const { premium_addons_data } = allData;

    let LicenseFields = (addon, index) => {
        let license = 'undefined' != typeof addon ? addon : []
        return <>
            {
                'undefined' != typeof license &&
                <div className={`license-details__item license-details__item__${'undefined' != typeof license._option_prefix ? license._option_prefix : ''}`}>
                    <h3>{license.item_name}</h3>
                    {!license.status ?
                        <a href={license.license_link} title="Add License & Activate">Add License & Activate</a>
                        :
                        <a href={license.account_link} title="Manage License">Manage License</a>
                    }
                </div>
            }
        </>
    }

    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                <br />
                {__('Get WP Travel Pro modules for payment, trip extras, Inventory management and other premium features.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]

    {
        premium_addons_data && 'undefined' != typeof premium_addons_data && premium_addons_data.length > 0 &&
        <>
            {
                premium_addons_data.map((addons, index) => {
                    content = [
                        ...content,
                        LicenseFields(addons, index)
                    ]

                })
            }
        </>
    }
    return content
});