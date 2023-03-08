import React from 'react';
import DatePicker from 'react-datepicker';
import { PanelRow } from  '@wordpress/components'
const DurationValidation = ( {allData } ) => {
  return (
    <>
        <PanelRow>
            <label>Duration End Date </label>
            <DatePicker
            selected={''}
            onChange={ () => {

            } }
            />
        </PanelRow>
    </>
  )
}

export default DurationValidation
