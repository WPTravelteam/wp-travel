import { __ } from '@wordpress/i18n';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib
const _ = lodash
const TripTimes =  ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;
	
	// @todo selectedTime & selectedTimeObject consist same value just type is different. need to remove selectedTimeObject.
	const { nomineeTimes, selectedTime, selectedTimeObject } = bookingData;
	return <div className="wp-travel-booking__selected-time">
		{nomineeTimes.length > 0 && <>
			<h4>{`${__i18n.bookings.available_trip_times}`}</h4>
			{
				nomineeTimes.map((timeObject, i) => {
					return <button key={i} disabled={timeObject.isSame( selectedTimeObject ) } onClick={ () => {
								updateBookingData( {
									selectedTime: timeObject.format('HH:mm'),
									selectedTimeObject: timeObject.toDate() // just to check selected trip time value. need to remove this latter.
								} );
							}
						} >
							{timeObject.format('h:mm A')}
						</button>
					
				})
			}
		</>}
	</div>
}

export default TripTimes