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
     * @since 2.2.3
     */
    public static function output(){

        echo self::trip_details( $trip_id='55489' );

        WP_Travel()->notices->print_notices( $type= 'error' );
        
    }

    /**
     * Trip Details.
     * @since 2.2.3
     */
    public static function trip_details( $trip_id ){

        if ( '' == $trip_id ) {
            WP_Travel()->notices->add( __( 'Trip ID not found', 'wp-travel' ), $type='error' );
            return;
        }
        ob_start();
        ?>
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
