import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextareaControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        enable_trip_enquiry_option,
        enable_og_tags,
        wp_travel_gdpr_message,
        open_gdpr_in_new_tab,
        options
        } = allData;


    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Miscellaneous Options', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Enable Trip Enquiry', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ enable_trip_enquiry_option == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                enable_trip_enquiry_option: 'yes' == enable_trip_enquiry_option ? 'no': 'yes'
                            })
                        } }
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
                <label>{ __( 'GDPR Message', 'wp-travel' ) }</label>
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
                <label>{ __( 'Open GDPR in new tab', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ open_gdpr_in_new_tab == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                open_gdpr_in_new_tab: 'yes' == open_gdpr_in_new_tab ? 'no': 'yes'
                            })
                        } }
                    />
                </div>
            </PanelRow>
            
            {applyFilters( 'wp_travel_settings_tab_misc_options_fields', [] )}
        </ErrorBoundary>
    </div>
}