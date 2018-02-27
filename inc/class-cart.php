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

        // echo self::trip_details( $trip_id='55489' );

       // WP_Travel()->notices->print_notices( $type= 'error' );
        ?>

       

<div class="wp-travel-ecommerse">
<form action="http://travelwp.physcode.com/cart/" method="post">
<table class="shop_table shop_table_responsive cart" cellspacing="0">
<thead>
<tr>
<th class="product-remove">&nbsp;</th>
<th class="product-thumbnail">&nbsp;</th>
<th class="product-name">Tour</th>
<th class="product-price">Price</th>
<th class="product-quantity">Number ticket</th>
<th class="product-subtotal">Total</th>
</tr>
</thead>
<tbody>
<tr class="cart_item">
<td class="product-remove">
<a href="http://travelwp.physcode.com/cart/?remove_item=140f6969d5213fd0ece03148e62e461e&amp;_wpnonce=ca0a7cd07a" class="remove" title="Remove this item" data-product_id="159" data-product_sku="">×</a> </td>
<td class="product-thumbnail">
<a href="http://travelwp.physcode.com/product/compact-shelter/"><img width="150" height="100" src="//travelwp.physcode.com/wp-content/uploads/2013/06/123596-150x100.jpg" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt=""></a> </td>
<td class="product-name" data-title="Tour">
<div><a href="http://travelwp.physcode.com/product/compact-shelter/">Compact Shelter</a></div> </td>
<td class="product-price" data-title="Price">
<span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>40.00</span> </td>
<td class="product-quantity" data-title="Number ticket">
<div class="quantity">
<input type="number" class="input-text qty text" step="1" min="0" max="" name="cart[140f6969d5213fd0ece03148e62e461e][qty]" value="2" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
</div>
</td>
<td class="product-subtotal" data-title="Total">
$80.00 </td>
</tr>
<tr>
<td colspan="6" class="actions">
<div class="coupon">
<label for="coupon_code">Coupon:</label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="Coupon code">
<input type="submit" class="button" name="apply_coupon" value="Apply Coupon">
</div>
<input type="submit" class="button" name="update_cart" value="Update Cart">
<input type="hidden" id="_wpnonce" name="_wpnonce" value="ca0a7cd07a"><input type="hidden" name="_wp_http_referer" value="/cart/"> </td>
</tr>
</tbody>
</table>
</form>
<div class="cart-collaterals">
<div class="cart_totals ">
<h2>Cart totals</h2>
<table cellspacing="0" class="shop_table shop_table_responsive">
<tbody><tr class="cart-subtotal">
<th>Subtotal</th>
<td data-title="Subtotal"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>80.00</span></td>
</tr>
<tr class="order-total">
<th>Total</th>
<td data-title="Total"><strong><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>80.00</span></strong> </td>
</tr>
</tbody></table>
<div class="wc-proceed-to-checkout">
<a href="http://travelwp.physcode.com/checkout/" class="checkout-button button alt wc-forward">
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
