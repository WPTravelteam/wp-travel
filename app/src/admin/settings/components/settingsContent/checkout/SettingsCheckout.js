import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl, Notice } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../../fields/VersionCompare'
import { alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const { updateSettings } = dispatch('WPTravel/Admin');
    const { enable_multiple_travellers, enable_one_page_booking } = allData;

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Checkout", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More checkout settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_cart_checkout_fields', [], allData)}
                    <PanelRow>
                        <label>{__('Enable multiple travelers', 'wp-travel')}</label>
                        <div id="wp-travel-checkout-enable-multiple-travelers" className="wp-travel-field-value">
                            <ToggleControl
                                checked={enable_multiple_travellers == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_multiple_travellers: 'yes' == enable_multiple_travellers ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('Collect multiple travelers information from checkout page.', 'wp-travel')}</p>
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{__( 'Enable On-Page Booking', 'wp-travel' ) }</label>
                        <div id="wp-travel-checkout-enable-multiple-travelers" className="wp-travel-field-value">
                            <ToggleControl 
                                checked={ typeof enable_one_page_booking != 'undefined' && enable_one_page_booking || false }
                                onChange={ (value ) => {
                                    updateSettings({
                                        ...allData,
                                        enable_one_page_booking : value
                                    })
                                }}
                            />
                            <p className="description">{__('To book a trip without redirecting to the checkout page.', 'wp-travel')}</p>
                            <p className="description warning">{__('To enable On-Page booking, make sure you have disabled multiple checkout (Add to cart) option.', 'wp-travel')}</p>
                        </div>
                    </PanelRow>
                </ErrorBoundary>
            </div>
        </>
    )
}

// Checkout Notice
addFilter('wp_travel_settings_tab_cart_checkout_fields', 'WPTravel/Settings/Checkout/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Checkout options ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get checkout option features and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});