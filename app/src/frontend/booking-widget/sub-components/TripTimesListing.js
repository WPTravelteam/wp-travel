import { __ } from '@wordpress/i18n';

const TripTimesListing =  ({ selected, options, onTimeSelect }) => {

	return <div className="wp-travel-booking__selected-time">
		{options.length > 0 && <>
			<h4>{`${__('Available times')}`}</h4>
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

export default TripTimesListing