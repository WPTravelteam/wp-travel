import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../fields/VersionCompare'
import { alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const { updateSettings } = dispatch('WPTravel/Admin');
    const { enable_multiple_travellers } = allData;

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
                        <div className="wp-travel-field-value">
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
                </ErrorBoundary>
            </div>
        </>
    )
}