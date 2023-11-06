import { __ } from '@wordpress/i18n';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib

import _ from 'lodash';
const TripTimes =  ( props ) => {
	// Component Props.
	const { bookingData, updateBookingData, date  } = props;

	const { twentyfour_time_format, enable_time } = date;
	const { selectedDate, nomineeTimes } = bookingData;
	return enable_time  && <div className="wp-travel-booking__selected-time">
		{nomineeTimes.length > 0 && <>
			<h4>{`${__i18n.bookings.available_trip_times}`}</h4>
			{
				nomineeTimes.map((timeObject, i) => {
					return <button key={i} disabled={timeObject.isSame( selectedDate ) } onClick={ () => {
								updateBookingData( {
									// isLoading:true,
									selectedTime: timeObject.format('HH:mm'),
								} );
							}
						} >
	
						{
							twentyfour_time_format &&
							timeObject.format('HH:mm')
							||
							timeObject.format('h:mm A')
						}
						</button>
					
				})
			}
		</>}
	</div>
}

export default TripTimes