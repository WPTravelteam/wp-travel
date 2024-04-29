import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextareaControl } from '@wordpress/components';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        enable_trip_enquiry_option,
        wp_travel_gdpr_message,
        open_gdpr_in_new_tab,
    } = allData;

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.miscellaneous.miscellaneous_settings}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More miscellaneous Settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.miscellaneous.gdpr_message}</label>
                        <div id="wp-travel-misc-gdpr-message" className="wp-travel-field-value">
                            <TextareaControl
                                value={wp_travel_gdpr_message}
                                onChange={
                                    (value) => {
                                        updateSettings({
                                            ...allData,
                                            wp_travel_gdpr_message: value
                                        })
                                    }
                                }
                            />
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.miscellaneous.open_gdpr_new_tab}</label>
                        <div id="wp-travel-misc-open-gdpr-tab" className="wp-travel-field-value">
                            <ToggleControl
                                checked={open_gdpr_in_new_tab == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        open_gdpr_in_new_tab: 'yes' == open_gdpr_in_new_tab ? 'no' : 'yes'
                                    })
                                }}
                            />
                        </div>
                    </PanelRow>
                </ErrorBoundary>
            </div>
        </>
    )
}
