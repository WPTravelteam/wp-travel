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
        <!--<div class="wp-travel-billing">            
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
		</style> -->
        <div class="checkout-page-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-sm-4 col-sm-push-8">
                        <div class="sticky-sidebar">
                            <div class="checkout-block checkout-right">
                                <div class="price-summary-wrapper">
                                    <h4 class="heading mt-0 text-primary uppercase">My Trip</h4>
                                    <ul class="price-summary-list">
                                        <li>
                                            <h6 class="heading mt-0 mb-0">Annapurna base camp</h6>
                                            <p>8 days 7 nights city tour</p>
                                        </li>
                                        <li>
                                            <h6 class="heading mt-0 mb-0">Starts in Kathmandu, Nepal</h6>
                                            <p>Monday, july 7, 2018</p>
                                        </li>
                                        <li>
                                            <h6 class="heading mt-0 mb-0">Ends in Kathmandu, Nepal</h6>
                                            <p>Thursday, july 10, 2018</p>
                                        </li>
                                        <li>
                                            <h6 class="heading mt-0 mb-0">What's included</h6>
                                            <p>Accommodation, Guide, Meals, Bus</p>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <h6 class="heading mt-20 mb-5 text-primary uppercase">Price per person</h6>
                                            <div class="row gap-10 mt-10">
                                            <div class="col-xs-7 col-sm-7">
                                                Price
                                            </div>
                                            <div class="col-xs-5 col-sm-5 text-right">
                                                $1458
                                            </div>
                                            </div>
                                            <div class="row gap-10 mt-10">
                                            <div class="col-xs-7 col-sm-7">
                                                Tax 10%
                                            </div>
                                            <div class="col-xs-5 col-sm-5 text-right">
                                                $145.8
                                            </div>
                                            </div>
                                        </li>
                                        <li class="divider"></li>
                                        <li class=" font600 font14 clearfix">
                                        <div class="row gap-10 mt-10">
                                            <div class="col-xs-7 col-sm-7">
                                                <strong >Total:</strong>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 text-right">
                                                <strong>$1623.5</strong>
                                            </div>
                                        </div>    
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <div class="row gap-10 font600 font14">
                                            <div class="col-xs-9 col-sm-9">
                                                Number of Travellers
                                            </div>
                                            <div class="col-xs-3 col-sm-3 text-right">
                                                1
                                            </div>
                                            </div>
                                        </li>
                                        <li class="total-price">
                                            <div class="row gap-10">
                                            <div class="col-xs-6 col-sm-6">
                                                <h5 class="heading mt-0 mb-0 text-white">Amount due</h5>
                                                <p class="text-white">before departure</p>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 text-right">
                                                <span class="block font20 font600 mb-5">$1458</span>
                                                <span class="font10 line10 block">**Best Price Guarantee </span>
                                            </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                            
                        </div>
                    <div class="col-sm-8 col-sm-pull-4">
                        <div class="checkout-block checkout-left">
                            <div class="section-title text-left">
                                <h3>Annapurna Base Camp<small> / 8 days 7 nights</small></h3>
                            </div>
                            
                            <div class="panel-group number-accordion">
                                <div class="panel ws-theme-timeline-block">
                                    <div class="panel-heading">
                                    <div class="ws-theme-timeline-img">
                                        1
                                    </div> 
                                    <h4 class="panel-title">Your selected departure date </h4>
                                    </div>
                                    <div id="number-accordion1" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <p>Your departure date: June 26, 2018 - June 29, 2016 <a href="#" class="btn btn-block simple">change</a></p>

                                    </div>
                                    </div>
                                </div>
                                <div class="panel ws-theme-timeline-block">
                                    <div class="panel-heading">
                                    <div class="ws-theme-timeline-img">
                                        2
                                    </div> 
                                    <h4 class="panel-title">Traveller Details</h4>
                                    </div>
                                    <div id="number-accordion2" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <div class="payment-content">
                                            <div class="payment-traveller">
                                                <div class="row gap-0">
                                                    <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
                                                        <h6 class="heading mt-0 mb-15">Traveller 1</h6>
                                                    </div>

                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">First Name:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="text" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Last Name:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="text" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20 select2-input-hide">
                                                        <label class="col-sm-4 col-md-3 control-label">Gender:</label>
                                                        <div class="col-sm-4 col-md-3">
                                                            <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Gender" tabindex="-1" aria-hidden="true">
                                                                <option value="">Gender</option>
                                                                <option value="male">Male.</option>
                                                                <option value="female">Female.</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20 select2-input-hide">
                                                        <label class="col-sm-4 col-md-3 control-label">DOB:</label>
                                                        <div class="col-sm-8 col-md-6">
                                                            <div class="row gap-15">
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Date" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Date</option>
                                                                        <option value="01">01</option>
                                                                        <option value="02">02</option>
                                                                        <option value="03">03</option>
                                                                        <option value="04">04</option>
                                                                        <option value="05">05</option>
                                                                        <option value="06">06</option>
                                                                        <option value="07">07</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Month" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Month</option>
                                                                        <option value="jan">Jan</option>
                                                                        <option value="feb">Feb</option>
                                                                        <option value="mar">Mar</option>
                                                                        <option value="apr">Apr</option>
                                                                        <option value="may">May</option>
                                                                        <option value="jun">Jun</option>
                                                                        <option value="jul">Jul</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Year" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Year</option>
                                                                        <option value="1985">1985</option>
                                                                        <option value="1986">1986</option>
                                                                        <option value="1987">1987</option>
                                                                        <option value="1988">1988</option>
                                                                        <option value="1900">1900</option>
                                                                        <option value="1901">1901</option>
                                                                        <option value="1902">1902</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Email:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="email" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Phone Number:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="email" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Nationality:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <select class="select2-single form-control select2-hidden-accessible" data-placeholder="Nationality" tabindex="-1" aria-hidden="true">
                                                                <option value="">Nationality</option>
                                                                <option value="Thai">Thai</option>
                                                                <option value="Malaysian">Malaysian</option>
                                                                <option value="Indonesian">Indonesian</option>
                                                                <option value="American">American</option>
                                                                <option value="England">England</option>
                                                                <option value="German">German</option>
                                                                <option value="Polish">Polish</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="payment-traveller">
                                                <a href="#" class="pull-right font12 traveller-remove"><i class="fa fa-times-circle"></i></a>
                                                <div class="row gap-0 gap-15">
                                                    <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
                                                        <h6 class="heading mt-0 mb-15">Traveller 2</h6>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">First Name:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="text" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Last Name:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <input type="text" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20 select2-input-hide">
                                                        <label class="col-sm-4 col-md-3 control-label">Gender:</label>
                                                        <div class="col-sm-4 col-md-3">
                                                            <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Gender" tabindex="-1" aria-hidden="true">
                                                                <option value="">Gender</option>
                                                                <option value="male">Male.</option>
                                                                <option value="female">Female.</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20 select2-input-hide">
                                                        <label class="col-sm-4 col-md-3 control-label">DOB:</label>
                                                        <div class="col-sm-8 col-md-6">
                                                            <div class="row gap-15">
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Date" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Date</option>
                                                                        <option value="01">01</option>
                                                                        <option value="02">02</option>
                                                                        <option value="03">03</option>
                                                                        <option value="04">04</option>
                                                                        <option value="05">05</option>
                                                                        <option value="06">06</option>
                                                                        <option value="07">07</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Month" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Month</option>
                                                                        <option value="jan">Jan</option>
                                                                        <option value="feb">Feb</option>
                                                                        <option value="mar">Mar</option>
                                                                        <option value="apr">Apr</option>
                                                                        <option value="may">May</option>
                                                                        <option value="jun">Jun</option>
                                                                        <option value="jul">Jul</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-xs-4 col-sm-4">
                                                                    <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Year" tabindex="-1" aria-hidden="true">
                                                                        <option value="">Year</option>
                                                                        <option value="1985">1985</option>
                                                                        <option value="1986">1986</option>
                                                                        <option value="1987">1987</option>
                                                                        <option value="1988">1988</option>
                                                                        <option value="1900">1900</option>
                                                                        <option value="1901">1901</option>
                                                                        <option value="1902">1902</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-horizontal">
                                                    <div class="form-group gap-20">
                                                        <label class="col-sm-4 col-md-3 control-label">Nationality:</label>
                                                        <div class="col-sm-8 col-md-9">
                                                            <select class="select2-single form-control select2-hidden-accessible" data-placeholder="Nationality" tabindex="-1" aria-hidden="true">
                                                                <option value="">Nationality</option>
                                                                <option value="Thai">Thai</option>
                                                                <option value="Malaysian">Malaysian</option>
                                                                <option value="Indonesian">Indonesian</option>
                                                                <option value="American">American</option>
                                                                <option value="England">England</option>
                                                                <option value="German">German</option>
                                                                <option value="Polish">Polish</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button class="btn btn-block simple center-div">Add another traveller</button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="panel ws-theme-timeline-block">
                                    <div class="panel-heading">
                                    <div class="ws-theme-timeline-img">
                                        3
                                    </div> 
                                    <h4 class="panel-title">Billing Address</h4>
                                    </div>
                                    <div id="number-accordion3" class="panel-collapse collapse in">
                                    <div class="panel-body">
                                        <div class="payment-content">
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Address:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <input type="text" class="form-control" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">City:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <input type="text" class="form-control" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Postal:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <input type="text" class="form-control" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Province:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <input type="text" class="form-control" value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Conntry:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <select class="form-control " data-placeholder="Conntry" tabindex="-1" aria-hidden="true">
                                                            <option value="">Conntry</option>   
                                                            <option value="Thai">Thai</option>
                                                            <option value="Malaysian">Malaysian</option>
                                                            <option value="Indonesian">Indonesian</option>
                                                            <option value="American">American</option>
                                                            <option value="England">England</option>
                                                            <option value="German">German</option>
                                                            <option value="Polish">Polish</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="panel ws-theme-timeline-block">
                                    <div class="panel-heading">
                                    <div class="ws-theme-timeline-img">
                                        4
                                    </div> 
                                    <h4 class="panel-title">Finish Payment /  secure</h4>
                                    </div>
                                    <div id="number-accordion4" class="panel-collapse collapse in">
                                    <div class="panel-body">

                                        <div class="payment-content">
                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Booking Options:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <select class="form-control " data-placeholder="Conntry" tabindex="-1" aria-hidden="true">
                                                            <option value="">Booking with payment</option>
                                                            <option value="">Booking only</option>
                                                        </select>
                                                    </div>    
                                                </div>
                                            </div>

                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Payment Gateway:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <select class="form-control " data-placeholder="Conntry" tabindex="-1" aria-hidden="true">
                                                            <option value="">Standard Paypal</option>
                                                            <option value="">Paypal express checkout</option>
                                                            <option value="">Stripe Checkout</option>
                                                        </select> 
                                                    </div>    
                                                </div>
                                            </div>

                                            <div class="form-horizontal">
                                                <div class="form-group gap-20">
                                                    <label class="col-sm-4 col-md-3 control-label">Payment Mode:</label>
                                                    <div class="col-sm-8 col-md-9">
                                                        <select class="form-control " data-placeholder="Conntry" tabindex="-1" aria-hidden="true">
                                                            <option value="">Partial Payment</option>
                                                            <option value="">Full Payment</option>
                                                        </select> 
                                                    </div>    
                                                </div>
                                            </div>

                                            <div class="wp-travel-form-field full-width hide-in-admin wp-travel-text-info">
                                                <label for="wp-travel-payment-trip-price-initial">
                                                    Trip Price <span class="required-label">*</span>
                                                </label>
                                                <div class="wp-travel-text-info"><span class="wp-travel-currency-symbol">$</span> <span class="wp-travel-info-content" id="wp-travel-payment-trip-price-initial">300.00</span></div>
                                            </div>
                                            <div class="wp-travel-form-field full-width hide-in-admin wp-travel-text-info">
                                                <label for="wp-travel-payment-tax-percentage-info">
                                                    Tax <span class="required-label">*</span>
                                                </label>
                                                <div class="wp-travel-text-info"><span class="wp-travel-currency-symbol"></span> <span class="wp-travel-info-content" id="wp-travel-payment-tax-percentage-info">10.00 %</span></div>
                                            </div>

                                            <div class="wp-travel-form-field full-width hide-in-admin wp-travel-text-info">
                                                <label for="wp-travel-payment-amount-info">
                                                    Payment Amount ( 50.00 %) <span class="required-label">*</span>
                                                </label>
                                                <div class="wp-travel-text-info"><span class="wp-travel-currency-symbol">$</span> <span class="wp-travel-info-content" id="wp-travel-payment-amount-info">165.00</span></div>
                                            </div>
                                            <div class="checkbox-block">
                                                <input id="accept_booking" name="accept_booking" type="checkbox" class="checkbox" value="paymentsCreditCard">
                                                <label class="" for="accept_booking">By submitting a booking request, you accept our <a href="#">terms and conditions</a> as well as the <a href="#">cancellation policy</a> and  <a href="#">House Rules</a> .</label>
                                            </div>
                                            <div class="wp-travel-form-field button-field">
                                                <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book and Pay"> 
                                            </div>




                                        </div>        
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                        </div> 
                        
                    </div>
            </div><!--container -->
        </div>
    <?php
	}
}

new WP_Travel_Checkout();
