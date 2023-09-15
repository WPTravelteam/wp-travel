import { PanelRow, TextControl, TextareaControl, SelectControl, RadioControl, CheckboxControl } from '@wordpress/components'
import { _n, __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
//version 6.2.0
const EnquiryForm = ( { allData } ) => {
 const { wp_travel_form_field, wp_travel_form_field_data } = allData;
 const { updateEnquiry } = dispatch('WPTravel/Enquiry');

 return (
   <div>
     {wp_travel_form_field.map( (res , index ) => {
       return <PanelRow key={res.form_field.priority + index } >
         { res.form_field.type != 'textarea' && res.form_field.type != 'select' && res.form_field.type != 'radio' && res.form_field.type != 'checkbox' &&
           <>
             <label><strong>{__( res.form_field.label, 'wp-travel')}</strong></label>
             <div>
               <TextControl
                 value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name]} 
                 required
                 type={ res.form_field.type }
                 onChange={
                     (value) => {
                         updateEnquiry({
                           ...allData,
                           ...wp_travel_form_field_data, [res.form_field.name] : value
                         })
                     }
                 }

               />
             </div>
           </>
           || res.form_field.type == 'textarea' && <>
             <label><strong>{__( res.form_field.label, 'wp-travel')}</strong></label> 
             <div>
               <TextareaControl value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] } required rows ="6"
                 onChange={
                   (value) => {
                     updateEnquiry({
                       ...allData, [res.form_field.name] : value
                       })
                     }
                   }

               />
             </div>
           </> || res.form_field.type == 'select' && <>
             <label><strong>{__( res.form_field.label, 'wp-travel')}</strong></label>
             <div >
               <SelectControl
                 value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] }
                 options={ typeof res.form_field.options != 'undefined' && (res.form_field.options).map( ( item , index ) => {
                   return Object.assign( {}, { ['label'] : item, ['value'] : item } )
                 })  }
                 onChange={ 
                   (value) => {
                     updateEnquiry({
                       ...allData, [res.form_field.name] : value
                       })
                   }
                 }
                      
               />
             </div>
           </> || res.form_field.type == 'radio' && <>
             <label><strong>{__( res.form_field.label, 'wp-travel')}</strong></label>
             <div >
               <RadioControl
                 // value={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] }
                 selected={ allData[res.form_field.name] && allData[res.form_field.name] || wp_travel_form_field_data[res.form_field.name] }
                 options={ typeof res.form_field.options != 'undefined' && (res.form_field.options).map( ( item , index ) => {
                   return Object.assign( {}, { ['label'] : item, ['value'] : item } )
                 })  }
                 onChange={ 
                   (value) => {
                     updateEnquiry({
                       ...allData, [res.form_field.name] : value
                       })
                   }
                 }
                      
               />
             </div>
           </>
         }
     </PanelRow>
     })}
   </div>
 )
}

export default EnquiryForm
