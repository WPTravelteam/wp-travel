import { _n, __} from '@wordpress/i18n'
const i18n = _wp_travel.strings;

import { wpTravelFormat } from '../../../_wptravelFunctions';

export default ( { coupon_data, currency_symbol, booking_option, payment_mode, partial_enable } ) => {
    const { cart_total, discount, total, tax, total_partial } = coupon_data;
    const payable_price = typeof total != 'undefined' && total || '0';
    const discount_get  = typeof discount != 'undefined' && discount || '0';
    const cart_payable  = typeof cart_total != 'undefined' && cart_total || '0';
    return <>
        <div className="wptravel-onepage-payment-total-trip-price">
            <div className="components-panel__body is-opened wptrave-on-page-booking-price-with-coupon wptrave-onpage-price-calculation">
                { parseInt( cart_payable ) > 0 && ( parseInt( tax ) > 0 || parseInt( discount_get ) > 0 ) &&  <div className="components-panel__row">
                    <label>{ i18n.set_cart_total_price }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( cart_payable ) }}></span>
                    </div>
                </div> }
                { parseInt( discount_get ) > 0 && <div className="components-panel__row">
                    <label>{ i18n.set_cart_discount }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( discount_get ) }}></span>
                    </div>
                </div>}
                { parseInt( tax ) > 0 && <div className="components-panel__row">
                    <label>{ i18n.set_cart_tax }
                        <span className='wptravel-onpage-trip-tax-prcnt' > ( {_wp_travel.trip_tax_enable == 'yes' &&  _wp_travel.trip_tax_percentage || 13}% )</span>
                    </label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( tax ) }}></span>
                    </div>
                </div>}
                
                { parseInt( payable_price ) > 0 && <div className="components-panel__row">
                    <label>{ i18n.set_payment_price }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( payable_price ) }}></span>
                    </div>
                </div>}
                { booking_option == "booking_with_payment" && payment_mode == 'partial' &&  partial_enable == 'yes' && <div className="components-panel__row">
                    <label>{i18n.set_cart_partial_payment }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( total_partial ) }}></span>
                    </div>
                </div>}
            </div>
        </div>
    </>
}