const __i18n = {
	..._wp_travel.strings
}

const Notice = ({ children, className }) => {
	return <div className="wp-travel-booking__notice is-info">
		<svg id="Capa_1" enableBackground="new 0 0 524.235 524.235" height="512" viewBox="0 0 524.235 524.235" width="512"><path d="m262.118 0c-144.53 0-262.118 117.588-262.118 262.118s117.588 262.118 262.118 262.118 262.118-117.588 262.118-262.118-117.589-262.118-262.118-262.118zm17.05 417.639c-12.453 2.076-37.232 7.261-49.815 8.303-10.651.882-20.702-5.215-26.829-13.967-6.143-8.751-7.615-19.95-3.968-29.997l49.547-136.242h-51.515c-.044-28.389 21.25-49.263 48.485-57.274 12.997-3.824 37.212-9.057 49.809-8.255 7.547.48 20.702 5.215 26.829 13.967 6.143 8.751 7.615 19.95 3.968 29.997l-49.547 136.242h51.499c.01 28.356-20.49 52.564-48.463 57.226zm15.714-253.815c-18.096 0-32.765-14.671-32.765-32.7.6.0-18.096 14.669-32.765 32.765-32.765s32.765 14.669 32.765 32.7.6.0 18.095-14.668 32.765-32.765 32.765z"></path></svg>
		{children}
	</div>
}

const InventoryNotice = ({ inventory }) => {
	const {
		wp_travel_inventory_sold_out_action,
		wp_travel_inventory_sold_out_message,
	} = inventory

	if (wp_travel_inventory_sold_out_action == 'show_sold_out_msg_only') {
		return <p className="wp-travel-sold-out">{wp_travel_inventory_sold_out_message}</p>
	}
	if (wp_travel_inventory_sold_out_action == 'allow_trip_enquiry') {
		return <p className="wp-travel-sold-out">
			<a
				className="wp-travel-send-enquiries"
				data-effect="mfp-move-from-top"
				href="#wp-travel-enquiries"
				onClick={() => {
					let enquiryBtn = document.getElementById('wp-travel-send-enquiries')
					enquiryBtn && enquiryBtn.click()
				}}>
				{__i18n.trip_enquiry}
			</a>
		</p>
	}
	return <p>{__i18n.bookings.pricing_not_available}</p>
}
export { Notice }
export default InventoryNotice ;