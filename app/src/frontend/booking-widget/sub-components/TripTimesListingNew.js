import { __ } from '@wordpress/i18n';

const __i18n = {
	..._wp_travel.strings
}

const TripTimesListingNew =  ({ selected, options, onTimeSelect }) => {

	return <div className="wti-booking__selected-time">
		{options.length > 0 && <>
			<h4>{`${__i18n.bookings.available_trip_times}`}</h4>
			{
				options.map((timeObject, i) => {
					// let timeObject = moment(`${selectedDate.toDateString()} ${time}`) // TODO: save times to state as date object.
					return <button key={i} disabled={timeObject.isSame(selected)} onClick={onTimeSelect(timeObject)}>
						{timeObject.format('h:mm A')}
					</button>
				})
			}
		</>}
	</div>
}

export default TripTimesListingNew