import { __ } from '@wordpress/i18n';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib
const _ = lodash
const TripTimes =  ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;
	
	const { selectedDate, nomineeTimes, selectedTime, selectedDateIds } = bookingData;
	const { dates }	= tripData;
	let enable_time = '';
	dates.map( res => {
		if( res.id == selectedDateIds[0] ) {
			enable_time =  res.enable_time;
		}
	});
	return enable_time && <div className="wp-travel-booking__selected-time">
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
							{timeObject.format('h:mm A')}
						</button>
					
				})
			}
		</>}
	</div>
}

export default TripTimes