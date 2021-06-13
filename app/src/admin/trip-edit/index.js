import { render, useEffect } from '@wordpress/element'; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from '@wordpress/components';
import { useSelect, select, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from '@wordpress/hooks';
import { sprintf, _n, __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

import './trip-store';
import './trip-pro-options';

import '../settings/store/settings-store'; // @since WP Travel 4.5.1

import SaveTripSection from './sub-components/SaveTripSection'

// Tab Items.
import WPTravelTripOptionsPriceDates from './trip-options-price-dates';
import WPTravelTripOptionsItinerary from './trip-options-itinerary';
import WPTravelTripOptionsTabs from './trip-options-tabs';
import WPTravelTripOptionsIncludesExcludes from './trip-options-includes-excludes';
import WPTravelTripOptionsFaq from './trip-options-faq';
import WPTravelTripOptionsGallery from './trip-options-gallery/'
import WPTravelTripOptionsFact from './trip-options-fact';
import WPTravelTripOptionsMisc from './trip-options-misc';
import WPTravelTripOptionsLocation from './trip-options-location';
import WPTravelTripOptionsCartAndCheckout from './trip-options-cart-checkout';
import WPTravelTripOptionsInventoryOptions from './trip-options-inventory-options';
import WPTravelTripOptionsDownloads from './trip-options-downloads';

const toggleDisablePostUpdate = ( isDisabled = false ) => {
    if( jQuery('#submitpost').find( '#wp-travel-post-disable-message' ).length < 1 && isDisabled ) {
        jQuery('#submitpost').append( `<div id="wp-travel-post-disable-message">${__('* Please save trip options first.')}</div>`)
        jQuery('#major-publishing-actions #publishing-action input#publish').attr('disabled', 'disabled')
        jQuery('#minor-publishing #save-action input#save-post').attr('disabled', 'disabled')
    } else if( !isDisabled) {
        jQuery('#submitpost').find( '#wp-travel-post-disable-message' ).remove();
        jQuery('#major-publishing-actions #publishing-action input#publish').removeAttr('disabled' )
        jQuery('#minor-publishing #save-action input#save-post').removeAttr('disabled')
    }
}
const __i18n = {
	..._wp_travel_admin.strings
}

const WPTravelTripOptions = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    toggleDisablePostUpdate(allData.has_state_changes);
    
    const settingsData = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);

    useEffect(() => {
        const { updateRequestSending } = dispatch('WPTravel/TripEdit');
        const { getTripData, getTripPricingCategories } = select('WPTravel/TripEdit');
        let store = getTripData(_wp_travel.postID);

        let tripCats = getTripPricingCategories();
        // let settings = getSettings();

    }, []);
    let wrapperClasses = "wp-travel-trip-pricings";
    wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters('wp_travel_trip_options_tabs', [
        {
            name: 'itinerary',
            title: __i18n.admin_tabs.itinerary,
            className: 'tab-itinerary',
            content: WPTravelTripOptionsItinerary
        },
        {
            name: 'price-dates',
            title: __i18n.admin_tabs.price_n_dates,
            className: 'tab-price-dates',
            content: WPTravelTripOptionsPriceDates
        },

        {
            name: 'includes-excludes',
            title: __('Includes/Excludes', 'wp-travel'),
            className: 'tab-includes-excludes',
            content: WPTravelTripOptionsIncludesExcludes
        },
        {
            name: 'facts',
            title: __('Facts', 'wp-travel'),
            className: 'tab-facts',
            content: WPTravelTripOptionsFact
        },
        {
            name: 'gallery',
            title: __('Gallery', 'wp-travel'),
            className: 'tab-gallery',
            content: WPTravelTripOptionsGallery
        },
        {
            name: 'locations',
            title: __('Locations', 'wp-travel'),
            className: 'tab-locations',
            content: WPTravelTripOptionsLocation
        },
        {
            name: 'cart-checkout',
            title: __('Checkout', 'wp-travel'), // cart & checkout label updated to checkout @since 4.4.3
            className: 'tab-cart-checkout',
            content: WPTravelTripOptionsCartAndCheckout
        },
        {
            name: 'inventory-options',
            title: __('Inventory Options', 'wp-travel'),
            className: 'tab-inventory-options',
            content: WPTravelTripOptionsInventoryOptions
        },
        {
            name: 'faqs',
            title: __('FAQs', 'wp-travel'),
            className: 'tab-faqs',
            content: WPTravelTripOptionsFaq
        },
        {
            name: 'downloads',
            title: __('Downloads', 'wp-travel'),
            className: 'tab-downloads',
            content: WPTravelTripOptionsDownloads
        },
        {
            name: 'misc',
            title: __('Misc. Options', 'wp-travel'),
            className: 'tab-misc',
            content: WPTravelTripOptionsMisc
        },
        {
            name: 'tabs',
            title: __('Tabs', 'wp-travel'),
            className: 'tab-tabs',
            content: WPTravelTripOptionsTabs
        },
        
        
    ]);
    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        <TabPanel className="wp-travel-trip-edit-menu"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) => 'undefined' !== typeof tab.content ? <ErrorBoundary><tab.content /></ErrorBoundary> : <>Error.</>
            }
        </TabPanel>
        <SaveTripSection />
    </div>
};

addFilter('wp_travel_trip_cart_checkout_tab_content', 'wp_travel', (content) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need to add your checkout options?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can add your checkout options for all of your trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

addFilter('wp_travel_trip_inventory_tab_content', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need to add your inventory options?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can add your inventory options in all of your trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

addFilter('wp_travel_trip_downloads_tab_content', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need to add your downloads?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

addFilter('wp_travel_after_pricings_options', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need More Options ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});
addFilter('wp_travel_after_dates_options', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need More Options ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});


addFilter(
    'wp_travel_trip_extras_notice',
    'wp_travel',
    (content ) => {
        content = [
            <p class="description">
                {__( 'Need advance Trip Extras options?', 'wp-travel' )}<a href="https://wptravel.io/wp-travel-pro/" target="_blank" class="wp-travel-upsell-badge">{__( 'GET PRO', 'wp-travel') }</a>
            </p>,
            ...content,
        ]
        return content
    }
);

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-trip-options-wrap') && null !== document.getElementById('wp-travel-trip-options-wrap')) {
        render(<WPTravelTripOptions />, document.getElementById('wp-travel-trip-options-wrap'));
    }
});