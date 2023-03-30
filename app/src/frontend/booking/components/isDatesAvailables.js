import {addFilter } from '@wordpress/hooks';

// cutoff date calculation..
const isDateAvailables = (startDate, cutOffTime, times ) => {
    let nowDate = moment( new Date() ).utc().toDate();

    let co = new Date( startDate ) // start date as cutoff date.
    // let coh = co.getHours(); // extract current hours in start date.
    // let com = co.getMinutes();

    let availableToday = false;
    let hour = 0
    let min = 0;
    if ( times.length > 0 ) { // set compare date with time
        // let times = tripTime.split(',');
        let tripTimeData = []
        
        times.forEach(function( time ) {
            co = new Date( startDate ) // re initialize co to fix date override issue in loop.
            tripTimeData = time.split(':')
            hour = tripTimeData[0];  // Trip time hour
            min = tripTimeData[1];  // Trip time minute
            
            co.setHours(parseInt( hour ) ) // Set trip time hour to get actual trip date along with time
            co.setMinutes( parseInt( min ) ) // Set trip time min to get actual trip date along with time
            
            let h = parseInt( co.getHours() ) - parseInt(cutOffTime)
            co.setHours( h ); // set cutoff hours.
            
            if( nowDate < co ) {
                availableToday = true
            } 
            
        });
        return availableToday

    } else {
        co.setHours(0) // Set trip time hour to get actual trip date along with time
        co.setMinutes(0) // Set trip time min to get actual trip date along with time

        let h = parseInt(-cutOffTime)
        co.setHours( h ); // set cutoff hours.
        
        if( nowDate < co ) {
            availableToday = true
        } 
        return availableToday
    }
}
addFilter( 'wpTravelRecurringCutofDateFilterDateListView', 'wp-travel', (available, dateRules, tripData, date, data ) => { 

    let cutOffTime = 'undefined' != typeof tripData.cuttOffTime ? parseInt( tripData.cuttOffTime ) : 0; // in hours
    if ( dateRules.find(da => moment(moment(da).format("YYYY-MM-DD")).unix() === moment(moment(date).format('YYYY-MM-DD')).unix()) instanceof Date && cutOffTime ) {
        let times = data.trip_time && data.trip_time.split(',') || []
        console.log( 'date', date );
        return isDateAvailables( date, cutOffTime, times )
    }
    return available
}, 999, 5 );
addFilter( 'wpTravelCutofDateFilterDateListViewNonRecurring', 'wp-travel', (times, tripData, selectedDate ) => { 
    let cutOffTime = 'undefined' != typeof tripData.cuttOffTime ? parseInt( tripData.cuttOffTime ) : 0; // in hours
    
    if ( cutOffTime ) {
        let nowDate = moment( new Date() ).utc().toDate();
        // let selectedDate = date.start_date
        let co = moment( new Date(selectedDate) ).utc().toDate(); // start date as cutoff date. [need to reset time in selected date due to some date doesn't have time.]
        let tripTimeData = []
        let hour = 0
        let min = 0;
        times = times.filter( ( time ) => {
            co = moment( new Date(selectedDate) ).utc().toDate(); // re initialize co to fix date override issue in loop.
            tripTimeData = time.split(':')
            hour = tripTimeData[0];  // Trip time hour
            min = tripTimeData[1];  // Trip time minute
            
            co.setHours( parseInt( hour ) ) // Set trip time hour to get actual trip date along with time
            co.setMinutes( parseInt( min ) ) // Set trip time min to get actual trip date along with time
            
            let h = parseInt( co.getHours() ) - parseInt(cutOffTime)
            co.setHours( h ); // set cutoff hours.
            if( nowDate < co ) {
                return time
            }
        })
    }
    return times;
    
}, 999, 3 );