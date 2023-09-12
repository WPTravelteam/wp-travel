const __i18n = {
	..._wp_travel.strings
}

const DateListingTableHead = ( props ) => {
    return <thead className="thead-table">
		<tr>
			<th class="tablehead-booking-pricings" data-label={__i18n.bookings.pricings_list_label}>{__i18n.bookings.pricings_list_label}</th>
			<th class="tablehead-booking-person" data-label={__i18n.bookings.person}>{__i18n.bookings.person}</th>
			<th class="tablehead-booking-dates" data-label={__i18n.bookings.date}>{__i18n.bookings.date}</th>
		</tr>
	</thead>;
}
export default DateListingTableHead;