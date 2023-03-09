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
                    {__("Miscellaneous Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More miscellaneous Settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    <PanelRow>
                        <label>{__('Enable Trip Enquiry', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={enable_trip_enquiry_option == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_trip_enquiry_option: 'yes' == enable_trip_enquiry_option ? 'no' : 'yes'
                                    })
                                }}
                            />
                            {/* <p className="description">{__( 'Enable test mode to make test payment.', 'wp-travel' )}</p> */}
                        </div>
                    </PanelRow>
                    {/* <PanelRow>
                <label>{ __( 'Enable OG Tags', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ enable_og_tags == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                enable_og_tags: 'yes' == enable_og_tags ? 'no': 'yes'
                            })
                        } }
                    />
                </div>
            </PanelRow> */}

                    <PanelRow>
                        <label>{__('GDPR Message', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
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
                        <label>{__('Open GDPR in new tab', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
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
