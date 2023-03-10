import React, { useState } from 'react';
import DatePicker from 'react-datepicker';
import { PanelRow } from  '@wordpress/components'
import { dispatch } from '@wordpress/data'
const __i18n = {
	..._wp_travel_admin
}
const DurationValidation = ( {allData } ) => {
  const [mydate, MyDate ] = useState('');
  const [myend, MyEnd ] = useState('');
  const { updateTripData } = dispatch('WPTravel/TripEdit');

  const { trip_duration } = typeof allData != 'undefined' ? allData : [];
  console.log('date string', _wp_travel);
  const startParams =  {
		showMonthDropdown: true,
		// customInput: <DatePickerBtn />, // Just button with custom html.
		showYearDropdown: 'select',
		dropdownMode: "select",
		minDate: new Date(),
		maxDate: typeof trip_duration != 'undefined' && typeof trip_duration.end_date != 'undefined' && trip_duration.end_date || '',
		// onChange: selectTripDate,
		// filterDate: IsTourDate( props ),
		// locale:"DPLocale",
    // startDate:null,
    // endDate:null
	}
  const endParams =  {
		showMonthDropdown: true,
		// customInput: <DatePickerBtn />, // Just button with custom html.
		showYearDropdown: true,
		dropdownMode: "select",
		minDate: typeof trip_duration != 'undefined' && typeof trip_duration.start_date != 'undefined' && trip_duration.start_date || new Date(),
		// maxDate: maxDate,
		// onChange: selectTripDate,
		// filterDate: IsTourDate( props ),
		// locale:"DPLocale",
    // startDate:null,
    // endDate:null
	}
  console.log('added some file', allData);
  console.log('my dsata', trip_duration );
  return (
    <>
        <PanelRow>
            <label>Duration Start Date </label>
            <DatePicker
              selected={ typeof trip_duration != 'undefined' && typeof trip_duration.start_date != 'undefined' && trip_duration.start_date || '' }
              { ...startParams }
              onChange={ ( val ) =>{
                const newDuration = { ...trip_duration, start_date : val }
                updateTripData({ ...allData, trip_duration : newDuration });
              }}
            />
        </PanelRow>
        <PanelRow>
            <label>Duration End Date </label>
            <DatePicker
              selected={ typeof trip_duration != 'undefined' && typeof trip_duration.end_date != 'undefined' && trip_duration.end_date || '' }
              { ...endParams }
              onChange={ ( val ) =>{
                const newEndDuration = { ...trip_duration, end_date : val }
                updateTripData({ ...allData, trip_duration : newEndDuration });
              }}
            />
        </PanelRow>
    </>
  )
}

export default DurationValidation
