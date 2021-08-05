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

const WPTravelCoupon = () => {
    // Set Coupon in state first.
    useEffect(() => {
        const { setCoupon } = select('WPTravel/Coupon');
        setCoupon(_wp_travel.postID);
    }, []);

    // Get All Data.
    const allData = useSelect((select) => {
        return select('WPTravel/Coupon').getAllStore()
    }, []);
   
    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
    // wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

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
        
        
    ], allData );

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

