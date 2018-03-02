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

        $trip_id = ( isset( $_GET['trip-id'] ) && '' !== $_GET['trip-id'] ) ? $_GET['trip-id'] : ''; 

        if ( '' == $trip_id ) {

            return;
        }
        
        ?>
        <div class="wp-travel-ecommerse">
        <form action="#" method="post">
        <table class="shop_table shop_table_responsive cart" cellspacing="0">
        <thead>
            <tr>

                <th class="product-thumbnail">Image</th>
                <th class="product-name">Tour</th>
                <th class="product-price">Price</th>
                <th class="product-quantity">Number ticket</th>
                <th class="product-subtotal">Total</th>
            </tr>
        </thead>
            <tbody>
                <tr class="cart_item">

                    <td class="product-thumbnail">
                        <a href="#">
                            <img width="150" height="100" src="//travelwp.physcode.com/wp-content/uploads/2013/06/123596-150x100.jpg" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="">
                        </a> 
                    </td>
                    <td class="product-name" data-title="Tour">
                        <div><a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>"><?php echo esc_html( get_the_title( $trip_id ) ); ?></a></div> 
                    </td>
                    <td class="product-price" data-title="Price">
                        <span class="woocommerce-Price-amount amount">
                            <span class="woocommerce-Price-currencySymbol">$</span>40.00
                        </span> 
                    </td>
                    <td class="product-quantity" data-title="Number ticket">
                        <div class="quantity">
                            <input type="number" class="input-text qty text" step="1" min="0" max="" name="cart[140f6969d5213fd0ece03148e62e461e][qty]" value="2" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
                        </div>
                    </td>
                    <td class="product-subtotal" data-title="Total">
                    <?php echo wp_travel_get_actual_trip_price( $trip_id ); ?> 
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
                            <tr class="cart-subtotal">
                                <th>10% Tax</th>
                                <td data-title="tax"><span class="Price-tax tax"><span class="Price-currencySymbol">$</span>80.00</span></td>
                            </tr>
                            <tr class="cart-subtotal">
                                <th>Subtotal</th>
                                <td data-title="Subtotal"><span class="Price-amount amount"><span class="Price-currencySymbol">$</span>80.00</span></td>
                            </tr>

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

<?php 

        
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
