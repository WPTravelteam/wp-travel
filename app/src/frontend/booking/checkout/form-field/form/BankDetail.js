import { useSelect } from '@wordpress/data';
// import he from 'he'
// import parse from 'html-react-parser';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { _n, __} from '@wordpress/i18n';
const _i18n = {
    ..._wp-travel.strings
} 
// import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { banck_detail } = bookingData
    return banck_detail.length > 0 && <>
        <div className='wp-travel-one-page-bank-detail'>
            <h4>{_i18n.set_bank_detail}</h4>
            <table>
                <thead>
                    <tr>
                        <th>{_i18n.set_account_name}</th>
                        <th>{_i18n.set_account_number}</th>
                        <th>{_i18n.set_bank_name}</th>
                        <th>{_i18n.set_sort_code}</th>
                        <th>{_i18n.set_ibam}</th>
                        <th>{_i18n.set_swift}</th>
                        <th>{_i18n.set_routing_number}</th>
                    </tr>
                </thead>
                <tbody>
                    { banck_detail.map( ( bankData, index ) =>{
                        const { account_name, account_number, bank_name, iban, routing_number, sort_code, swift } = bankData;
                        return <tr key={index}>
                            <td>{ typeof account_name != 'undefined' && account_name || ''}</td>
                            <td>{ typeof account_number != 'undefined' && account_number || ''}</td>
                            <td>{ typeof bank_name != 'undefined' && bank_name || ''}</td>
                            <td>{ typeof sort_code != 'undefined' && sort_code || ''}</td>
                            <td>{ typeof iban != 'undefined' && iban || ''}</td>
                            <td>{ typeof swift != 'undefined' && swift || ''}</td>
                            <td>{ typeof routing_number != 'undefined' && routing_number || ''}</td>
                        </tr>
                    } )}
                </tbody>
            </table>
        </div>
    </>
}