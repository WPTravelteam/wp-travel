import { render, useEffect } from '@wordpress/element';
import { TabPanel, Spinner, Notice } from '@wordpress/components';
import { useSelect, select, dispatch } from '@wordpress/data';;
import { applyFilters, addFilter } from '@wordpress/hooks';
import { sprintf, _n, __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

import './store/coupon-store';

import SaveCoupons from './sub-components/SaveCoupons'

// Tab Items.
import General from './tab-components/General';
import Restriction from './tab-components/Restriction';

const toggleDisablePostUpdate = (isDisabled = false) => {
    if (jQuery('#submitpost').find('#wp-travel-post-disable-message').length < 1 && isDisabled) {
        jQuery('#submitpost').append(`<div id="wp-travel-post-disable-message">${__('* Please save coupon Enquiry first.')}</div>`)
        jQuery('#major-publishing-actions #publishing-action input#publish').attr('disabled', 'disabled')
        jQuery('#minor-publishing #save-action input#save-post').attr('disabled', 'disabled')
    } else if (!isDisabled) {
        jQuery('#submitpost').find('#wp-travel-post-disable-message').remove();
        jQuery('#major-publishing-actions #publishing-action input#publish').removeAttr('disabled')
        jQuery('#minor-publishing #save-action input#save-post').removeAttr('disabled')
    }
}

const WPTravelCoupon = () => {
    //  Set Coupon in state first.
    useEffect(() => {
        const { setCoupon } = select('WPTravel/Coupon');
        setCoupon(_wp_travel.postID);
    }, []);

    // Get All Data.
    const allData = useSelect((select) => {
        return select('WPTravel/Coupon').getAllStore()
    }, []);

    toggleDisablePostUpdate(allData.has_state_changes);

    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";

    // Add filter to tabs.
    let tabs = applyFilters('wptravel_coupon_tabs', [
        {
            name: 'general',
            title: __('General', 'wp-travel'),
            className: 'tab-general',
            content: General
        },
        {
            name: 'restriction',
            title: __('Restriction', 'wp-travel'),
            className: 'tab-restriction',
            content: Restriction
        },


    ], allData);


    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        <SaveCoupons position="top" />
        <TabPanel className="wp-travel-block-tabs wp-travel-settings-block-wrapper"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) => 'undefined' !== typeof tab.content ? <ErrorBoundary><tab.content /></ErrorBoundary> : <>{__('Error', 'wp-travel')}</>
            }
        </TabPanel>
        <SaveCoupons position="bottom" />
    </div>
};

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-coupon-block') && null !== document.getElementById('wp-travel-coupon-block')) {
        render(<WPTravelCoupon />, document.getElementById('wp-travel-coupon-block'));
    }
});

