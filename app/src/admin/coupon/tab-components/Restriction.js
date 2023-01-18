import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect, forwardRef } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, TextControl, FormTokenField, SelectControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import DatePicker, {registerLocale} from "react-datepicker";
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Coupon').getAllStore()
    }, []);
    const {general, restriction, options} = allData
    const coupon_limit_number = 'undefined' !== typeof restriction && 'undefined' !== typeof restriction.coupon_limit_number ? restriction.coupon_limit_number : '';
    let restricted_trips      = ( 'undefined' !== typeof restriction && 'undefined' !== typeof restriction.restricted_trips ) ? restriction.restricted_trips : [];
    let coupon_user_id        = ( 'undefined' !== typeof restriction && 'undefined' !== typeof restriction.coupon_user_id ) ? restriction.coupon_user_id : 0;
    const {trips, users} = options;
    let allTrips = 'undefined' != typeof trips ? trips : [];
    let tripSuggestions = []
    if (allTrips.length > 0) {
        tripSuggestions = allTrips.map(allTrip => {
            return { id: allTrip.id, title: allTrip.title }
        })
    }
    let restricted_trips_names = [];
    if (restricted_trips.length > 0) {
        restricted_trips.filter((restrictedId) => {
            let restricted_trips_name = allTrips.filter(tripSuggestion => {
                return tripSuggestion.id == restrictedId
            }).map(suggestion => { return suggestion.title })
            if (restricted_trips_name[0]) {
                restricted_trips_names = [...restricted_trips_names, restricted_trips_name[0]]
            }
        })
    }
    let userTravel = Array()
    let allUsers = users.map( userData => {
        if( userData.roles[0] !== 'travel-guide' ) { 
           userTravel = Array( {label:userData.data.user_login, value:userData.data.ID} )
         }
         return {label:userData.data.user_login, value:userData.data.ID }
    } );
    userTravel.unshift({label: 'Select User', value:'' }); 
    allUsers.unshift({label: 'Select User', value:'' }); 
    // Update Values
    const { updateCoupon } = dispatch('WPTravel/Coupon');

    return <div className="wp-travel-ui wp-travel-ui-card coupon-general">
        <h3>{ __( 'Restriction', 'wp-travel' ) }</h3>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Limit Coupon to Trips', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <FormTokenField
                        label=""
                        value={restricted_trips_names}
                        suggestions={tripSuggestions.map(tripSuggestion => { return tripSuggestion.title })}
                        onChange={
                            tokens => {
                                let filteredTripIds = [];
                                tokens.map((title) => {
                                    let selectedTripID = tripSuggestions.filter(tripSuggestion => {
                                        return tripSuggestion.title == title
                                    }).map(suggestion => { return suggestion.id })
                                    if (selectedTripID[0]) {
                                        filteredTripIds = [...filteredTripIds, selectedTripID[0]]
                                    }
                                })
                                updateCoupon({
                                    ...allData,
                                    restriction: {...restriction, restricted_trips: filteredTripIds }
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Coupon Using Limit', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={coupon_limit_number}
                        type="number"
                        min="1"
                        onChange={ 
                            (value) => {
                                updateCoupon({
                                    ...allData,
                                    restriction: {...restriction, coupon_limit_number: value }
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Coupon User', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <SelectControl
                        value={coupon_user_id}
                        options={userTravel}
                        onChange={ ( value ) => {
                            updateCoupon({
                                ...allData,
                                restriction: {...restriction, coupon_user_id: value }
                            })
                        } }
                    />
                </div>
            </PanelRow>
        </ErrorBoundary>
    </div>
}