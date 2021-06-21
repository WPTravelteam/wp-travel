import { Notice, PanelRow, TextControl, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element'
import { dispatch, useSelect } from '@wordpress/data';
import { addFilter, applyFilters } from '@wordpress/hooks';
import { sprintf, __ } from '@wordpress/i18n';
import Geocode from "react-geocode";
import { Gmaps, Marker } from 'react-gmaps';
import Autocomplete from 'react-google-autocomplete';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripOptionsLocationContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);

    const settingsData = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);

    const { map_data } = allData

    return <ErrorBoundary>
        <div className="wp-travel-trip-location">
            <h4>{__i18n.map}</h4>

            {applyFilters('wp_travel_admin_map_area', '', settingsData, map_data)}
        </div>
    </ErrorBoundary>;
}

const WPTravelTripOptionsLocation = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border">
        <WPTravelTripOptionsLocationContent />
    </div>
}

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

addFilter('wp_travel_admin_map_area', 'wp_travel', (content, settingsData, map_data) => {
    const { google_map_api_key, google_map_zoom_level, wp_travel_map } = settingsData

    if (wp_travel_map !== 'google-map') {
        return content;
    }
    if (!google_map_api_key) {
        content = [...content,
        <>
            <Notice status="warning" isDismissible={false}>
                <strong dangerouslySetInnerHTML={{ __html: sprintf(__(`${__i18n.notices.map_key_option.description}`), `<a href="edit.php?post_type=itinerary-booking&page=settings">`, `</a>`) }}></strong>
            </Notice><br />
        </>,
        <GmapIframe zoomlevel={google_map_zoom_level} />
        ]
        return content
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

    // const params = {v: '3.exp', key: 'YOUR_API_KEY'};

    const onDragEnd = (e) => {
        updateMapFromMarker(e)
    }
    content = [...content,
    <div className="wp-travel-gmap">
        <div className="wp-travel-autocomplete-wrap">
            {/* <Autocomplete
                apiKey={google_map_api_key}
                style={{ width: '90%' }}
                onPlaceSelected={(place) => {
                    updateMapFromAutocomplete(place)
                }}
                placeholder={map_data.loc}
                searchText={map_data.loc}
                types={['address']}
            /> */}
        </div>
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
    ]
    return content
});

addFilter('wp_travel_admin_map_area', 'wp_travel', (content, settingsData, map_data) => {
    content = [<>
        <Notice isDismissible={false} status="informational">
            <strong>{__i18n.notices.map_option.title}</strong>
            <br />
            {__i18n.notices.map_option.description}
            <br />
            <br />
            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
        </Notice><br />
    </>,
    ...content
    ]
    return content
});

export default WPTravelTripOptionsLocation;