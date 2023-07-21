import { _n, __} from '@wordpress/i18n'
export default ( { coupon_data, currency_symbol, booking_option, payment_mode, partial_enable } ) => {
    const { cart_total, discount, total, tax, total_partial } = coupon_data;
    const payable_price = typeof total != 'undefined' && total || '0';
    const discount_get  = typeof discount != 'undefined' && discount || '0';
    const cart_payable  = typeof cart_total != 'undefined' && cart_total || '0';
    return <>
        <div className="wptravel-onepage-payment-total-trip-price">
            <div className="components-panel__body is-opened wptrave-on-page-booking-price-with-coupon wptrave-onpage-price-calculation">
                { parseInt( cart_payable ) > 0 && ( parseInt( tax ) > 0 || parseInt( discount_get ) > 0 ) &&  <div className="components-panel__row">
                    <label>{ __( 'Trip Price', 'wp-travel' ) }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span className="wp-travel-trip-currency">{currency_symbol}</span> 
                        <span className="wp-travel-trip-price-figure">{cart_payable}</span>
                    </div>
                </div> }
                { parseInt( discount_get ) > 0 && <div className="components-panel__row">
                    <label>{ __( 'Discount', 'wp-travel' ) }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span className="wp-travel-trip-currency"><span className="wptravel-on-page-tax-increament-icon">-</span> {currency_symbol}</span> 
                        <span className="wp-travel-trip-price-figure">{discount_get}</span>
                    </div>
                </div>}
                { parseInt( tax ) > 0 && <div className="components-panel__row">
                    <label>{ __( 'Tax', 'wp-travel' ) }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span className="wp-travel-trip-currency"><span className="wptravel-on-page-tax-increament-icon">+</span> {currency_symbol}</span> 
                        <span className="wp-travel-trip-price-figure">{tax}</span>
                    </div>
                </div>}
                
                { parseInt( payable_price ) > 0 && <div className="components-panel__row">
                    <label>{ __( 'Total Trip Price', 'wp-travel' ) }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span className="wp-travel-trip-currency">{currency_symbol}</span> 
                        <span className="wp-travel-trip-price-figure">{payable_price}</span>
                    </div>
                </div>}
                { booking_option == "booking_with_payment" && payment_mode == 'partial' &&  partial_enable == 'yes' && <div className="components-panel__row">
                    <label>{ __( 'Partial Payment Price', 'wp-travel' ) }</label>
                    <div id="wp-travel-trip-price_info" className="wptravel-one-page-booking">			
                        <span className="wp-travel-trip-currency">{currency_symbol}</span> 
                        <span className="wp-travel-trip-price-figure">{total_partial}</span>
                    </div>
                </div>}
            </div>
        </div>
    </>
}