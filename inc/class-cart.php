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

		$trip_id = ( isset( $_GET['trip-id'] ) && '' !== $_GET['trip-id'] ) ? $_GET['trip-id'] : '';

		if ( '' == $trip_id ) {
			return;
		}
		$settings = wp_travel_get_settings();
		$trip_tax_details = wp_travel_process_trip_price_tax($post_id);

		$trip_price_tax = wp_travel_process_trip_price_tax( $trip_id );
		if ( isset( $trip_price_tax['actual_trip_price'] ) ) {

			$trip_price = $trip_price_tax['actual_trip_price'];
		}
		else {
			$trip_price = $trip_price_tax['trip_price'];
		}
		?>		
        <div class="wp-travel-ecommerse">
        <form action="#" method="post">
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
                            <span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><?php echo $trip_price; ?> 
                        </span> 
                    </td>
                    <td class="product-quantity" data-title="Number ticket">
                        <div class="quantity">
                            <input type="number" class="input-text wp-travel-pax text" step="1" min="1" max="" name="pax" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
                        </div>
                    </td>
                    <td class="product-subtotal" data-title="Total">
						<span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-product-subtotal">0</div>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
            <div class="cart-collaterals">
                <div class="cart_totals ">
                    <h2>Cart totals</h2>
                    <table cellspacing="0" class="shop_table shop_table_responsive">
                        <tbody>
							<?php if ( wp_travel_is_trip_price_tax_enabled() && isset( $trip_tax_details['tax_percentage'] ) && '' !== $trip_tax_details['tax_percentage']  ) { ?>
                            <tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td data-title="Subtotal"><span class="Price-amount amount"><span class="Price-currencySymbol">$</span>80.00</span></td>
                            </tr>

                            <tr class="cart-subtotal">
                                <th>10% Tax</th>
                                <td data-title="tax"><span class="Price-tax tax"><span class="Price-currencySymbol">$</span>80.00</span></td>
                            </tr>
							<?php } ?>

                            <tr class="order-total">
                                <th>Total</th>
                                <td data-title="Total"><strong><span class="Price-amount amount"><span class="Price-currencySymbol">$</span>80.00</span></strong> </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="wc-proceed-to-checkout">
                    <a href="#" class="checkout-button button alt wc-forward">
                    Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
		<script>
			function calculate_price() {
				var no_of_pax = jQuery( '.wp-travel-pax' ).val();
				var price = get_payable_price( 'full', no_of_pax );
				
				jQuery('.wp-travel-product-subtotal').html( price.trip_price );
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
