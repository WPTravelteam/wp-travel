const __i18n = {
	..._wp_travel.strings
}

const DateListingTableHead = ( props ) => {
    return <section className="thead-table parash-head">
		<span data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</span>
		<span data-label={__i18n.bookings.person}>{__i18n.bookings.person}</span>
		<span data-label={__i18n.bookings.date}>{__i18n.bookings.date}</span>

			{/* <span data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</span>
			<span data-label={__i18n.bookings.person}>{__i18n.bookings.person}</span>
			<span data-label={__i18n.bookings.date}>{__i18n.bookings.date}</span> */}
	</section>;
}
export default DateListingTableHead;