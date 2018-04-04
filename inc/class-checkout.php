<?php
/**
 * WP Travel Checkout.
 *
 * @package WP Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Travel Checkout Shortcode Class.
 */
class WP_Travel_Checkout {

	/**
	 * Constructor.
	 */
	function __construct() {
	}

	/**
	 * Output of checkout shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		if ( ! isset( $_REQUEST['trip_id'] ) ) {
			esc_html_e( 'Your Cart is empty', 'wp-travel' );
			return;
        }
        
		$trip_id = $_REQUEST['trip_id'];

		if ( $trip_id > 0 ) {
			$max_pax = get_post_meta( $trip_id, 'wp_travel_group_size', true );
		}
		$pax_size = 1;
		if ( isset( $_REQUEST['pax'] ) && ( $max_pax && $_REQUEST['pax'] <= $max_pax ) ){
			$pax_size = $_REQUEST['pax'];
		}
		$price_per = wp_travel_get_price_per_text( $trip_id );
		$settings = wp_travel_get_settings();		
        $trip_tax_details = wp_travel_process_trip_price_tax( $trip_id );
		if ( isset( $trip_tax_details['tax_type'] ) && 'inclusive' === $trip_tax_details['tax_type'] ) {
            $trip_price = $trip_tax_details['actual_trip_price'];
		} else {
            $trip_price = $trip_tax_details['trip_price'];
        }
        $trip_price_original = $trip_price;
        if ( strtolower( $price_per ) === 'person' ) {
            $trip_price *= $pax_size;
		}
        
        ?>
        <div class="wp-travel-billing">            
            <div class="wp-travel-tab-wrapper">
                <div class="col-md-7 clearfix columns" >
					<h3><?php esc_html_e( 'Billing info' ) ?></h3>
                    <?php wp_travel_get_booking_form() ?>
                </div>
                
                <div class="col-md-5 columns">
                    <div class="order-wrapper">
                    <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wp-travel' ) ?></h3>
                    <div id="order_review" class="wp-travel-checkout-review-order">
                        <table class="shop_table wp-travel-checkout-review-order-table">
                            <thead>
                                <tr>
                                <th class="product-name"><?php esc_html_e( 'Trip', 'wp-travel' ) ?></th>
                                <th class="product-total"><?php esc_html_e( 'Total', 'wp-travel' ) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="cart_item">
                                <td class="product-name">
                                    <?php echo esc_html( get_the_title( $trip_id ) ); ?> &nbsp; <strong class="product-quantity">× <span class="wp-travel-cart-pax"><?php echo esc_html( $pax_size ); ?></span><?php printf( ' pax' ); ?> </strong> 
                                </td>
                                <td class="product-total">
                                <span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="product-total-price amount" payment_price="<?php echo @$trip_tax_details['actual_trip_price'] ?>" trip_price="<?php echo $trip_price_original; ?>" >0</span>
                                </td>
                                </tr>
                            </tbody>
                            <tfoot>
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
                                <th><?php esc_html_e( 'Total' ) ?></th>
                                <td><strong><span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="wp-travel-total-price-amount amount">0</span></strong> </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    </div>
                </div>
            </div>        
        </div>
		<style>
		.wp-travel-tab-wrapper .wp-travel-booking-form-wrapper form{width:100%; padding-left:0; padding-right:0}
		</style>
    <?php
	}
}

new WP_Travel_Checkout();
