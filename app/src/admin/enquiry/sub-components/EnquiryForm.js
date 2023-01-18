// @since 6.1.0
import { PanelRow, TextControl, TextareaControl } from '@wordpress/components'
import { _n, __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
const EnquiryForm = ( { allData } ) => {
   const { wp_travel_form_field, wp_travel_form_field_data } = allData;
   const { updateEnquiry } = dispatch('WPTravel/Enquiry');
 return (
   <div>
     {wp_travel_form_field.map( res => {
       return <PanelRow key={res.form_field.priority} >
         <label><strong>{__( res.form_field.label, 'wp-travel')}</strong></label>
         <div >
             { res.form_field.type !== 'textarea' && <>
             <TextControl
                 value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] } 
                 required
                 onChange={
                     (value) => {
                         updateEnquiry({
                           ...allData,
                           ...wp_travel_form_field_data, [res.form_field.name] : value
                         })
                     }
                 }

             /> </>  ||
             <TextareaControl value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] } required rows ="6"
               onChange={
                 (value) => {
                   updateEnquiry({
                    ...allData,
                    ...wp_travel_form_field_data, [res.form_field.name] : value
                     })
                   }
                 }

               />
             }
         </div>
     </PanelRow>
     })}
   </div>
 )
}

export default EnquiryForm
