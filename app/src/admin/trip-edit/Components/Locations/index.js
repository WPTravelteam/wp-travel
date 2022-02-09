import { Notice, PanelRow, TextControl, ToggleControl, Button, PanelBody, BaseControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element'
import { dispatch, useSelect } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { sprintf, __ } from '@wordpress/i18n';
import Geocode from "react-geocode";
import { Gmaps, Marker } from 'react-gmaps';
import Autocomplete from 'react-google-autocomplete';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
import { isEmpty, has } from 'lodash';
// @todo Need to remove this in future.
// const WPTravelTripOptionsLocation = () => {
//     return <></>;
// }
// export default WPTravelTripOptionsLocation;

const GmapIframe = props => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const location = allData.map_data && allData.map_data.loc || ''
    const lat = allData.map_data && allData.map_data.lat || ''
    const lng = allData.map_data && allData.map_data.lng || ''
    const useLatLng = allData.map_data && allData.map_data.use_lat_lng || 'no'
    const zoomlevel = props.zoomlevel || 15
    const iframeHeight = 400
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    const updateMapData = (name, toggle) => value => {
        if (toggle) {
            updateTripData({
                ...allData,
                map_data: {
                    ...allData.map_data,
                    [name]: 'yes' === useLatLng ? 'no' : 'yes'
                }
            })
        } else {
            updateTripData({
                ...allData,
                map_data: {
                    ...allData.map_data,
                    [name]: value
                }
            })
        }
    }
    const q = 'yes' === useLatLng ? `${lat},${lng}` : location
    return <>
        { ! props.isBlock &&<>
        <PanelRow>
            <label>{__i18n.location}</label>
            <ToggleControl
                checked={'yes' === useLatLng}
                help={__i18n.help_text.enable_location}
                onChange={updateMapData('use_lat_lng', true)}
            />
        </PanelRow>
        {'yes' !== useLatLng &&
            <PanelRow>
                <label>{__i18n.enter_location}</label>
                <TextControl
                    value={location}
                    onChange={updateMapData('loc')}
                />
            </PanelRow> ||
            <>
                <PanelRow>
                    <label>{__i18n.latitude}</label>
                    <TextControl
                        value={lat}
                        onChange={updateMapData('lat')}
                    />
                </PanelRow>
                <PanelRow>
                    <label>{__('Longitude')}</label>
                    <TextControl
                        value={lng}
                        onChange={updateMapData('lng')}
                    />
                </PanelRow>
            </>}
        </>}
        
        <PanelRow>
            <div className="wp-travel-map-wrap">
                <div className="wp-travel-map">
                    <div className="wp-travel-map__container">
                        <iframe width="100%" id="wpTravelMap" height={400} src={`https://maps.google.com/maps?q=${q}&t=m&z=${zoomlevel}&output=embed&iwloc=near`}></iframe>
                    </div>
                </div>
            </div>
        </PanelRow>
    </>
}

// Single Components for hook callbacks.
const Locations = ({allData}) => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);
    const { map_data } = allData
    return <ErrorBoundary>
        <div className="wp-travel-trip-location">
            <h4>{__i18n.map}</h4>
            {applyFilters('wp_travel_admin_map_area', [], settingsData, map_data)}
        </div>
    </ErrorBoundary>;
}

const MapNotice = ( {settingsData, map_data } ) => {
    return <Notice isDismissible={false} status="informational">
        <strong>{__i18n.notices.map_option.title}</strong>
        <br />
        {__i18n.notices.map_option.description}
        <br />
        <br />
        <Button href="https://wptravel.io/wp-travel-pro/" isSmall target="_blank" isSecondary>{__i18n.notice_button_text.get_pro}</Button>
    </Notice>
}

const GoogleMap = ( {settingsData, map_data, isBlock } ) => {
    if ( ! settingsData ) {
        return <></>;
    }
    const { google_map_api_key, google_map_zoom_level, wp_travel_map } = settingsData

    if (wp_travel_map !== 'google-map') {
        return <></>;
    }
    if ( ! google_map_api_key ) {
        return <div className="wptravel-gmap-iframe">
            <Notice status="warning" isDismissible={false}>
                <strong dangerouslySetInnerHTML={{ __html: sprintf(__(`${__i18n.notices.map_key_option.description}`), `<a href="edit.php?post_type=itinerary-booking&page=settings">`, `</a>`) }}></strong>
            </Notice><br />
            <GmapIframe zoomlevel={google_map_zoom_level} isBlock={isBlock} />
        </div>;
    }

    let zoom = (google_map_zoom_level) ? parseInt(google_map_zoom_level) : 15;
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    const updateMapFromMarker = (e) => {

        let _alMapData = map_data;
        _alMapData.lat = e.latLng.lat()
        _alMapData.lng = e.latLng.lng()
        _alMapData.loc = ''

        Geocode.setApiKey(google_map_api_key);
        Geocode.fromLatLng(e.latLng.lat(), e.latLng.lng()).then(
            response => {
                // quick fix.
                _alMapData.loc = response.results[0].formatted_address;
                updateTripData({
                    ...allData,
                    map_data: { ..._alMapData }
                })
            },
            error => {
                console.error(error);
            }
        );

        updateTripData({
            ...allData,
            map_data: { ..._alMapData }
        })
    }

    const updateMapFromAutocomplete = (place) => {

        const { map_data } = allData;

        let _alMapData = map_data;
        _alMapData.lat = place.geometry.location.lat()
        _alMapData.lng = place.geometry.location.lng()
        _alMapData.loc = place.formatted_address
        // _alMapData.searching = false

        updateTripData({
            ...allData,
            map_data: { ..._alMapData }
        })

    }
    const coords = {
        lat: map_data && map_data.lat,
        lng: map_data && map_data.lng
    };


    const onDragEnd = (e) => {
        updateMapFromMarker(e)
    }

    return <div className="wp-travel-gmap">
        { ! isBlock &&
        <div className="wp-travel-autocomplete-wrap">
            <Autocomplete
                apiKey={google_map_api_key}
                style={{ width: '90%' }}
                onPlaceSelected={(place) => {
                    updateMapFromAutocomplete(place)
                }}
                placeholder={map_data && map_data.loc}
                searchText={map_data && map_data.loc}
                types={['address']}
            />
        </div>
        }
        <Gmaps
            width={'100%'}
            height={'400px'}
            lat={coords.lat}
            lng={coords.lng}
            zoom={zoom}
            loadingMessage={__i18n.loading}
        >
            <Marker
                lat={coords.lat}
                lng={coords.lng}
                draggable={true}
                onDragEnd={onDragEnd}
            />
        </Gmaps>
        <br />
    </div>
    
}

const GoogleMapBlockControls = ({ location, changeLocation, settings }) => {
    
    if ( ! settings ) {
        return <></>;
    }
    const { wp_travel_map, google_map_api_key } = settings;
    if (wp_travel_map !== 'google-map') {
        return <></>;
    }

    const address = has( location, 'loc' ) && ! isEmpty( location.loc ) ? location.loc : '';
    const lat = 'undefined' != typeof location && location.lat ? location.lat : '';
    const lng = 'undefined' != typeof location && location.lng ? location.lng : '';
    const useLatLng = 'undefined' != typeof location && location.use_lat_lng && 'no' !== location.use_lat_lng ? true : false;
    const hasApiKey = 'undefined' != typeof location && settings.google_map_api_key;
    return (
        <Fragment>
            <PanelBody
                /* translators: block settings */
                title={ __( 'Map Settings' ) }
            >
                <Notice status="warning" isDismissible={ false }>
                    <strong>Note:</strong> Making changes on following setting will also change trip location.
                </Notice>
                <hr />
                <ToggleControl label={ __( 'Enable Latitude/ Longitude' ) } checked={ useLatLng } onChange={ ( value ) => {
                    changeLocation( {
                        use_lat_lng: value ? 'yes' : 'no',
                    } );
                } } />
                <Fragment>
                    { ! useLatLng && 
                        <>
                        { ! hasApiKey ? 
                        
                            <TextControl label={ __( 'Location' ) } value={ address } onChange={ ( value ) => {
                                changeLocation( {
                                    loc: value,
                                } );
                            } } /> 
                        :  
                            <>
                            <BaseControl id="map-location"
                                label={ __( 'Location' ) } help={ __( 'Enter location to search.' ) }>
                                <Autocomplete
                                    apiKey={google_map_api_key}
                                    style={{ width: '100%' }}
                                    onPlaceSelected={(place) => {
                                        const data = {};
                                        data.lat = String( place.geometry.location.lat() );
                                        data.lng = String( place.geometry.location.lng() );
                                        data.loc = place.formatted_address;
                                        data.use_lat_lng = 'no';
                                        changeLocation( data );
                                    }}
                                    onChange={ (e) => {
                                        changeLocation( {
                                            loc: e.target.value,
                                        } );
                                    } }
                                    placeholder={location && location.loc}
                                    searchText={location && location.loc}
                                    types={['address']}
                                    value={address}
                                />
                            </BaseControl>
                            
                            </>
                        }
                        </>
                    }
                    { useLatLng && 
                    <div>
                        <TextControl label={ __( 'Latitude' ) } value={ lat } onChange={ ( value ) => {
                            changeLocation( {
                                lat: value,
                            } );
                        } } />
                        <TextControl label={ __( 'Longitude' ) } value={ lng } onChange={ ( value ) => {
                            changeLocation( {
                                lng: value,
                            } );
                        } } />
                    </div> }

                    { ! hasApiKey &&
                        <Notice isDismissible={ false }>
                            { __( 'You can add \'Google Map API Key\' in the settings to use additional features.' ) }
                        </Notice>
                    }
                </Fragment> 
                {/* } */}
            </PanelBody>
        </Fragment>
    );
}


// Callbacks.
const LocationsCB = ( content, allData ) => {
    return [ ...content, <Locations allData={allData} key="Locations" /> ];
}

const MapNoticeCB = ( content, settingsData, map_data ) => {
    return [ ...content, <MapNotice settingsData={settingsData} map_data={map_data} key="MapNotice" /> ];
}

const GoogleMapCB = ( content, settingsData, map_data ) => {
    return [ ...content, <GoogleMap settingsData={settingsData} map_data={map_data} key="GoogleMap" isBlock={false} /> ];
}
const GoogleMapBlockCB = ( content, settingsData, map_data ) => {
    return [ ...content, <GoogleMap settingsData={settingsData} map_data={map_data} key="GoogleMap" isBlock={true} /> ];
}

const GoogleMapBlockControlsCB = ( content, settingsData, map_data, props ) => {
    return [ ...content, <GoogleMapBlockControls {...props} /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_locations', 'WPTravel/TripEdit/Locations', LocationsCB, 10 ); // Main Wrapper hook. other hooks are inside its content.

addFilter('wp_travel_admin_map_area', 'WPTravel/TripEdit/Locations/MapNotice',  MapNoticeCB, 10 ); // Need to remove this hook from pro.
// Maps.
addFilter('wp_travel_admin_map_area', 'WPTravel/TripEdit/Locations/GoogleMap',  GoogleMapCB, 20 );


addFilter( 'wptravel_trip_edit_block_map_area', 'WPTravel/TripEdit/Block/Maps/MapsNoticeFields', MapNoticeCB, 10 );
addFilter( 'wptravel_trip_edit_block_map_area', 'WPTravel/TripEdit/Block/Locations/GoogleMapControls', GoogleMapBlockControlsCB, 20 ); // Google Map block edit mode.
addFilter( 'wptravel_trip_edit_block_map_area_view', 'WPTravel/TripEdit/Block/Locations/GoogleMap', GoogleMapBlockCB );   // Google Map block view mode.
