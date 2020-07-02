import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';
import Select from 'react-select'

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        hide_related_itinerary,
        enable_multiple_travellers
        } = allData;
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h4>{ __( 'Trip Settings', 'wp-travel' ) }</h4>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Hide related trips', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ hide_related_itinerary == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                hide_related_itinerary: 'yes' == hide_related_itinerary ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'This will hide your related trips.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Enable multiple travelers', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ enable_multiple_travellers == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                enable_multiple_travellers: 'yes' == enable_multiple_travellers ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Collect multiple travelers information from checkout page.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            
        </ErrorBoundary>
    </div>
}