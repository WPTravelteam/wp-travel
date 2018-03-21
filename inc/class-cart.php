<?php
/**
 * WP Travel Cart.
 *
 * @package WP Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Travel Cart Shortcode Class.
 */
class WP_Travel_Cart {

	/**
	 * Constructor.
	 */
	function __construct() {

	}

	/**
	 * Output of cart shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {

		$trip_id = ( isset( $_GET['trip_id'] ) && '' !== $_GET['trip_id'] ) ? $_GET['trip_id'] : '';
		$trip_duration = ( isset( $_GET['trip_duration'] ) && '' !== $_GET['trip_duration'] ) ? $_GET['trip_duration'] : 1;
        
		if ( '' == $trip_id ) {
			return;
		}
		$settings = wp_travel_get_settings();		
		$trip_tax_details = wp_travel_process_trip_price_tax( $trip_id );
		if ( isset( $trip_tax_details['tax_type'] ) && 'inclusive' === $trip_tax_details['tax_type'] ) {

			$trip_price = $trip_tax_details['actual_trip_price'];
		} else {
			$trip_price = $trip_tax_details['trip_price'];
		}
		$checkout_page_id = '';
		$checkout_page_url = get_home_url();

		if ( isset( $settings['checkout_page_id'] ) ) {
			$checkout_page_id = $settings['checkout_page_id'];
			$checkout_page_url = get_permalink( $checkout_page_id );
		} ?>
        <div class="wp-travel-ecommerse wp-travel-cart-items">
        <form action="<?php echo esc_url( $checkout_page_url ) ?>" method="get">
		<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ) ?>" >
		<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ) ?>" >
        <table class="shop_table shop_table_responsive cart" cellspacing="0">
        <thead>
            <tr>

                <th class="product-thumbnail"><?php esc_html_e( 'Image', 'wp-travel' ); ?></th>
                <th class="product-name"><?php esc_html_e( 'Tour', 'wp-travel' ); ?></th>
                <th class="product-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></th>
                <th class="product-quantity"><?php esc_html_e( 'PAX', 'wp-travel' ); ?></th>
                <th class="product-subtotal"><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
            </tr>
        </thead>
            <tbody>
                <tr class="cart_item">

                    <td class="product-thumbnail">
                        <a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>">
							<?php echo wp_kses( wp_travel_get_post_thumbnail( $trip_id ), wp_travel_allowed_html( array( 'img' )  ) ); ?>
                        </a> 
                    </td>
                    <td class="product-name" data-title="Tour">
                        <div><a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>"><?php echo esc_html( get_the_title( $trip_id ) ); ?></a></div> 
                    </td>
                    <td class="product-price" data-title="Price">
                        <span class="woocommerce-Price-amount amount">
                            <span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-trip-price" payment_price="<?php echo @$trip_tax_details['actual_trip_price'] ?>" trip_price="<?php echo $trip_price; ?>" ><?php echo $trip_price ?></span>
                        </span> 
                    </td>
                    <td class="product-quantity" data-title="Number ticket">
                        <div class="quantity">
                            <input type="number" class="input-text wp-travel-pax text" step="1" min="1" <?php if($max_available = get_post_meta($trip_id,'wp_travel_group_size', true)): ?>max="<?php echo $max_available ?>" <?php endif; ?> name="pax" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
                        </div>
                    </td>
                    <td class="product-subtotal" data-title="Total">
						<span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-trip-total">0</span>
                    </td>
                </tr>
            </tbody>
        </table>
        
            <div class="cart-collaterals">
                <div class="cart_totals ">
                    <h2><?php esc_html_e( 'Cart totals', 'wp-travel' ); ?></h2>
                    <table cellspacing="0" class="shop_table shop_table_responsive">
                        <tbody>
							<?php if ( wp_travel_is_trip_price_tax_enabled() && isset( $trip_tax_details['tax_percentage'] ) && '' !== $trip_tax_details['tax_percentage'] && 'inclusive' !== $trip_tax_details['tax_type'] ) { ?>
                            <tr class="cart-subtotal">
                                <th><?php esc_html_e( 'Subtotal', 'wp-travel' )?></th>
                                <td data-title="Subtotal"><span class="Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-sub-total">0</span></td>
                            </tr>

                            <tr class="cart-subtotal">
                                <th>
									<?php
									if ( isset( $trip_tax_details['tax_percentage'] ) ) {
										echo esc_html( $trip_tax_details['tax_percentage'] );
										esc_html_e( '% Tax ', 'wp-travel' );
									} ?>
                                </th>
                                <td data-title="tax"><span class="Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-tax">0</span></td>
                            </tr>
							<?php } ?>

                            <tr class="order-total">
                                <th><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
                                <td data-title="Total"><strong><span class="Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-total">0</span></strong> </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="wc-proceed-to-checkout">
                    <input type="submit" class="button alt" value="<?php esc_html_e( 'Proceed to checkout', 'wp-travel' ) ?>">
                    </button>
                    </div>
                </div>
            </div>
		</form>
        </div>
		<script>
			function calculate_price() {
                var sub_total_including_tax = 0;
                var sub_total = 0;
                var price_per = wt_payment.price_per;

                jQuery( '.wp-travel-cart-items tbody tr.cart_item' ).each( function(){
                    no_of_pax = jQuery(this).find( '.wp-travel-pax' ).val();

                    var payment_price = jQuery(this).find('.wp-travel-trip-price').attr('payment_price'); // price including tax
                    var trip_price = jQuery(this).find('.wp-travel-trip-price').attr('trip_price'); // price excluding tax
                                        
                    if (price_per.toLowerCase().slice(0, 6) === 'person') {                        
                        payment_price = parseFloat(payment_price) * parseFloat(no_of_pax);
                        trip_price = parseFloat(trip_price) * parseFloat(no_of_pax);
                        trip_price = trip_price.toFixed(2);
                    }
                    // Individual Price total.
                    jQuery(this).find('.wp-travel-trip-total').html( trip_price );
                    
                    sub_total += parseFloat( trip_price );
                    total += sub_total;

                    var tax_fields = jQuery('.wp-travel-sub-total').length;
                    if ( tax_fields > 0 ) {
                        sub_total_including_tax += parseFloat( payment_price );                        
                    }
                   
                } );
                
                // Sub Total
                if (sub_total.toFixed)
                    sub_total = parseFloat( sub_total ).toFixed(2);

                var total = sub_total;
                    
                if ( sub_total_including_tax.toFixed ) {
                    sub_total_including_tax = parseFloat( sub_total_including_tax ).toFixed(2);
                    if ( sub_total_including_tax > total ) {
                        tax = parseFloat( sub_total_including_tax - total ).toFixed(2);
                        total = sub_total_including_tax;
                        jQuery('.wp-travel-tax').html( tax );
                    }
                }          
                
                jQuery('.wp-travel-sub-total').html( sub_total );                    
                jQuery('.wp-travel-total').html( total );
			}	

			jQuery(document).ready( function($){
				calculate_price();
				$(document).on( 'change', '.wp-travel-pax', function() {
					calculate_price();
				} );
			});
		</script>
	<?php
	}

	/**
	 * Trip Details.
	 *
	 * @param Number $trip_id Trip ID.
	 * @since 2.2.3
	 */
	public static function trip_details( $trip_id ) {

		if ( '' == $trip_id ) {
			WP_Travel()->notices->add( __( 'Trip ID not found', 'wp-travel' ), $type = 'error' );
			return;
		}
		ob_start(); ?>
        <h3><?php esc_html_e( 'Trip Overview', 'wp-travel' ); ?></h3>
        <table class="shop_table woocommerce-checkout-review-order-table">
            <thead>
                <tr>
                    <th class="product-name"><?php esc_html_e( 'Trip Details', 'wp-travel' ); ?></th>
                    <th class="product-total"><?php esc_html_e( 'Trip Price', 'wp-travel' ); ?></th>
                </tr>
            </thead>
        <tbody>
            <tr class="cart_item">
                <td class="product-name">
                    <strong><?php echo get_the_title( $trip_id ); ?></strong>
                </td>
                <td class="product-total">
                    <strong><?php echo wp_travel_get_actual_trip_price( $trip_id ); ?></strong>
                </td>
            </tr>
            <tr class="cart_item">
                <td class="product-name">
               	    <strong><?php echo esc_html( wp_travel_get_actual_trip_price( $trip_id ) ); ?></strong>
                </td>
                <td class="product-total">
               	
                </td>
            </tr>
        </tbody>
            <tfoot>

            <tr class="cart-subtotal">
            <th>Subtotal</th>
                <td>
                    
                </td>
            </tr>
            <tr class="order-total">
                <th>Total</th>
                <td>
                
                </td>
            </tr>
            </tfoot>
        </table>
		

        <?php
		$details = ob_get_clean();
		return $details;

	}
}

new WP_Travel_Cart();
