const __i18n = {
	..._wp_travel.strings
}

const DateListingTableHead = ( props ) => {
    return <thead className="thead-table">
		<tr>
			<th data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</th>
			<th data-label={__i18n.bookings.person}>{__i18n.bookings.person}</th>
			<th data-label={__i18n.bookings.date}>{__i18n.bookings.date}</th>
		</tr>
	</thead>;
}
export default DateListingTableHead;