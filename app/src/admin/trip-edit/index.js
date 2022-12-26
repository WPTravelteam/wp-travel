import { render, useEffect } from "@wordpress/element"; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from "@wordpress/components";
import { useSelect, select, dispatch } from "@wordpress/data"; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from "@wordpress/hooks";
import { _n, __ } from "@wordpress/i18n";
import domReady from "@wordpress/dom-ready";
import ErrorBoundary from "../../ErrorBoundry/ErrorBoundry";

import "./trip-store";

import "../settings/store/settings-store"; // @since 4.5.1

import SaveTripSection from "./sub-components/SaveTripSection";

// Tab Items.
// import WPTravelTripOptionsItinerary from './Components/Itinerary';
// import WPTravelTripOptionsPriceDates from './Components/PriceDates';
// import WPTravelTripOptionsIncludesExcludes from './Components/IncludesExcludes';
// import WPTravelTripOptionsFact from './Components/Facts';
// import WPTravelTripOptionsGallery from './Components/Gallery'
// import WPTravelTripOptionsLocation from './Components/Locations';
// import WPTravelTripOptionsCartAndCheckout from './Components/Checkout';
// import WPTravelTripOptionsInventoryOptions from './Components/Inventory';
// import WPTravelTripOptionsFaq from './Components/Faqs';
// import WPTravelTripOptionsDownloads from './Components/Downloads';
// import WPTravelTripOptionsMisc from './Components/Misc';
// import WPTravelTripOptionsTabs from './Components/Tabs';

import "./Components/Overview";
import "./Components/Itinerary";
import "./Components/PriceDates";
import "./Components/IncludesExcludes";
import "./Components/Facts";
import "./Components/Gallery";
import "./Components/Locations";
import "./Components/Checkout";
import "./Components/Inventory";
import "./Components/Faqs";
import "./Components/Downloads";
import "./Components/Misc";
import "./Components/Tabs";
import "./Components/Guide";

const toggleDisablePostUpdate = (isDisabled = false) => {
	if (
		jQuery("#submitpost").find("#wp-travel-post-disable-message").length <
			1 &&
		isDisabled
	) {
		jQuery("#submitpost").append(
			`<div id="wp-travel-post-disable-message">${__(
				"* Please save trip options first."
			)}</div>`
		);
		jQuery(
			"#major-publishing-actions #publishing-action input#publish"
		).attr("disabled", "disabled");
		jQuery("#minor-publishing #save-action input#save-post").attr(
			"disabled",
			"disabled"
		);
	} else if (!isDisabled) {
		jQuery("#submitpost").find("#wp-travel-post-disable-message").remove();
		jQuery(
			"#major-publishing-actions #publishing-action input#publish"
		).removeAttr("disabled");
		jQuery("#minor-publishing #save-action input#save-post").removeAttr(
			"disabled"
		);
	}
};
const __i18n = {
	..._wp_travel_admin.strings,
};

const WPTravelTripOptions = () => {
	const allData = useSelect((select) => {
		return select("WPTravel/TripEdit").getAllStore();
	}, []);
	toggleDisablePostUpdate(allData.has_state_changes);

	const settingsData = useSelect((select) => {
		return select("WPTravel/TripEdit").getSettings();
	}, []);

	useEffect(() => {
		const { updateRequestSending } = dispatch("WPTravel/TripEdit");
		const { getTripData, getTripPricingCategories } =
			select("WPTravel/TripEdit");
		let store = getTripData(_wp_travel.postID);

		let tripCats = getTripPricingCategories();
	}, []);
	let wrapperClasses = "wp-travel-trip-pricings";
	wrapperClasses = allData.is_sending_request
		? wrapperClasses + " wp-travel-sending-request"
		: wrapperClasses;

	// Add filter to tabs.
	let tabs = applyFilters("wp_travel_trip_options_tabs", []);
	return (
		<div className={wrapperClasses}>
			{allData.is_sending_request && <Spinner />}
			<TabPanel
				className="wp-travel-trip-edit-menu"
				activeClass="active-tab"
				onSelect={() => false}
				tabs={tabs}
			>
				{(tab) => (
					<ErrorBoundary>
						<div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border">
							{applyFilters(
								`wptravel_trip_edit_tab_content_${tab.name.replaceAll(
									"-",
									"_"
								)}`,
								[],
								allData
							)}
						</div>
					</ErrorBoundary>
				)}
			</TabPanel>
			<SaveTripSection />
		</div>
	);
};

addFilter("wp_travel_trip_options_tabs", "wp_travel", (tabs) => {
	return [
		...tabs,
		...[
			{
				name: "overview",
				title: __i18n.overview,
				className: "tab-overview",
				// content: WPTravelTripOptionsItinerary
			},
			{
				name: "itinerary",
				title: __i18n.admin_tabs.itinerary,
				className: "tab-itinerary",
				// content: WPTravelTripOptionsItinerary
			},
			{
				name: "price-dates",
				title: __i18n.admin_tabs.price_n_dates,
				className: "tab-price-dates",
				// content: WPTravelTripOptionsPriceDates
			},

			{
				name: "includes-excludes",
				title: __i18n.admin_tabs.includes_excludes,
				className: "tab-includes-excludes",
				// content: WPTravelTripOptionsIncludesExcludes
			},
			{
				name: "facts",
				title: __i18n.admin_tabs.facts,
				className: "tab-facts",
				// content: WPTravelTripOptionsFact
			},
			{
				name: "gallery",
				title: __i18n.admin_tabs.gallery,
				className: "tab-gallery",
				// content: WPTravelTripOptionsGallery
			},
			{
				name: "locations",
				title: __i18n.admin_tabs.locations,
				className: "tab-locations",
				// content: WPTravelTripOptionsLocation
			},
			{
				name: "cart-checkout",
				title: __i18n.admin_tabs.checkout, // cart & checkout label updated to checkout @since 4.4.3
				className: "tab-cart-checkout",
				// content: WPTravelTripOptionsCartAndCheckout
			},
			{
				name: "inventory-options",
				title: __i18n.admin_tabs.inventory_options,
				className: "tab-inventory-options",
				// content: WPTravelTripOptionsInventoryOptions
			},
			{
				name: "faqs",
				title: __i18n.admin_tabs.faqs,
				className: "tab-faqs",
				// content: WPTravelTripOptionsFaq
			},
			{
				name: "downloads",
				title: __i18n.admin_tabs.downloads,
				className: "tab-downloads",
				// content: WPTravelTripOptionsDownloads
			},
			{
				name: "tabs",
				title: __i18n.admin_tabs.tabs,
				className: "tab-tabs",
				// content: WPTravelTripOptionsTabs
			},
			{
				name: "misc",
				title: __i18n.admin_tabs.misc,
				className: "tab-misc",
				// content: WPTravelTripOptionsMisc
			},
			{
				name: "guides",
				title: __i18n.admin_tabs.guides,
				className: "tab-guides",
				// content: WPTravelTripOptionsTripGuide
			},
		],
	];
});



addFilter("wp_travel_after_dates_options", "WPTravel/TripEdit/PriceDates/MoreDatesNotice", (content, allData) => {
	content = [
		<>
			<Notice isDismissible={false} status="informational">
				<strong>{__i18n.notices.need_more_option.title}</strong>
				<br />
				{__i18n.notices.need_more_option.description}
				<br />
				<br />
				<a
					className="button button-primary"
					target="_blank"
					href="https://wptravel.io/wp-travel-pro/"
				>
					{__i18n.notice_button_text.get_pro}
				</a>
			</Notice>
			<br />
		</>,
		...content,
	];
	return content;
});

addFilter("wp_travel_trip_extras_notice", "WPTravel/TripEdit/TripExtrasNotice", (content) => {
	content = [
		<p className="description">
			{__i18n.notices.need_extras_option.title}
			<br />
			<a
				href="https://wptravel.io/wp-travel-pro/"
				target="_blank"
				className="wp-travel-upsell-badge"
			>
				{__i18n.notice_button_text.get_pro}
			</a>
		</p>,
		...content,
	];
	return content;
});

domReady(function () {
	if (
		"undefined" !==
			typeof document.getElementById("wp-travel-trip-options-wrap") &&
		null !== document.getElementById("wp-travel-trip-options-wrap")
	) {
		render(
			<WPTravelTripOptions />,
			document.getElementById("wp-travel-trip-options-wrap")
		);
	}
});
