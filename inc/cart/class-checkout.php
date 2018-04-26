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
		global $wt_cart;
		$trips = $wt_cart->getItems();
						
		if ( ! $trips ) {
			$wt_cart->cart_empty_message();
			return;
		} ?>
		<div class="checkout-page-wrap">
			<div class="container">
				<div class="row">
					<div class="col-sm-4 col-sm-push-8">
						<div class="sticky-sidebar">
							<div class="checkout-block checkout-right">
								<!-- <div class="price-summary-wrapper">
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
								</div> -->
								<?php include sprintf( '%s/inc/cart/cart-mini.php', WP_TRAVEL_ABSPATH ); ?>
							</div>
						</div>
					</div>
					<div class="col-sm-8 col-sm-pull-4">
						<div class="checkout-block checkout-left">							
							<?php include sprintf( '%s/inc/cart/checkout.php', WP_TRAVEL_ABSPATH ); ?>
						</div>							
					</div>
				</div>
			</div><!--container -->
		</div>
		<div class="wp-travel-billing">            
			<div class="wp-travel-tab-wrapper">
				<div class="col-md-7 clearfix columns" >
					<h3><?php esc_html_e( 'Billing info', 'wp-travel' ) ?></h3>
					<?php wp_travel_get_booking_form() ?>
				</div>

				<div class="col-md-5 columns">			
					<?php include sprintf( '%s/inc/cart/cart-mini.php', WP_TRAVEL_ABSPATH ); ?>					
				</div>
			</div>        
		</div>
	<?php
	}

	/**
	 * Validate pricing Key
	 *
	 * @return bool true | false.
	 */
	public static function is_pricing_key_valid( $trip_id, $pricing_key ) {

		if ( '' === $trip_id || '' === $pricing_key ) {

			return false;
		}

		//Get Pricing variations.
		$pricing_variations = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

			$result = array_filter($pricing_variations, function( $single ) use ( $pricing_key ) {
				return in_array( $pricing_key, $single, true );
			});
			return ( '' !== $result && count( $result ) > 0 ) ? true : false;
		}
		return false;

	}

	/**
	 * Validate date
	 *
	 * @return bool true | false.
	 */
	public static function is_request_date_valid( $trip_id, $pricing_key, $test_date ) {

		if ( '' === $trip_id || '' === $pricing_key || '' === $test_date ) {

			return false;
		}

		$trip_multiple_date_options = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = wp_travel_get_pricing_variation_start_dates( $trip_id, $pricing_key );

		if ( 'yes' === $trip_multiple_date_options && is_array( $available_dates ) && ! empty( $available_dates ) ) {

			return in_array( $test_date, $available_dates, true );
		}
		else {

			$date_now  = ( new DateTime() )->format( 'Y-m-d' );
			$test_date = ( new DateTime( $test_date ) )->format( 'Y-m-d' );

			if ( strtotime( $date_now ) <= strtotime( $test_date ) ) {

				return true;
			}

			return false;

		}
	}

	function get_checkout_traveller_fields() {
		$travellers_fileds = array(
			'first_name'	=> array(
				'type' => 'text',
				'label' => __( 'First Name', 'wp-travel' ),
				'name' => 'wp_travel_fname',
				'id' => 'wp-travel-fname',
				'validations' => array(
					'required' => true,
					'maxlength' => '50',					
				),
				'priority' => 10,
			),
	
			'last_name'		=> array(
				'type' => 'text',
				'label' => __( 'Last Name', 'wp-travel' ),
				'name' => 'wp_travel_lname',
				'id' => 'wp-travel-lname',
				'validations' => array(
					'required' => true,
					'maxlength' => '50',					
				),
				'priority' => 20,
			),
			'gender'		=> array(
				'type' => 'select',
				'label' => __( 'Gender', 'wp-travel' ),
				'name' => 'wp_travel_gender',
				'id' => 'wp-travel-country',
				'options' => array( 'male' => __( 'Male', 'wp-travel' ), 'female' => __( 'Female', 'wp-travel' ), 'other' => __( 'Other', 'wp-travel' ) ),
				'validations' => array(
					'required' => true,
				),
				'priority' => 30,
			),

			'dob_day'		=> array(
				'type' => 'select',
				'label' => __( 'Day', 'wp-travel' ),
				'name' => 'wp_travel_dob_day',
				'id' => 'wp-travel-dob-day',
				'options' => array( 1,2,3,4,5,6,7,8,9,10 ),
				'validations' => array(
					'required' => true,
				),
				'priority' => 40,
			),
			'dob_month'		=> array(
				'type' => 'select',
				'label' => __( 'Month', 'wp-travel' ),
				'name' => 'wp_travel_dob_month',
				'id' => 'wp-travel-dob-month',
				'options' => array( 1,2,3,4,5,6,7,8,9,10,11,12 ),
				'validations' => array(
					'required' => true,
				),
				'priority' => 50,
			),

			'dob_year'		=> array(
				'type' => 'select',
				'label' => __( 'Year', 'wp-travel' ),
				'name' => 'wp_travel_dob_day',
				'id' => 'wp-travel-dob-day',
				'options' => array( 2018,2017,2016 ),
				'validations' => array(
					'required' => true,
				),
				'priority' => 60,
			),

			'email' => array(
				'type' => 'email',
				'label' => __( 'Email', 'wp-travel' ),
				'name' => 'wp_travel_email',
				'id' => 'wp-travel-email',
				'validations' => array(
					'required' => true,
					'maxlength' => '60',
				),
				'priority' => 70,
			),
			'phone_number'	=> array(
				'type' => 'text',
				'label' => __( 'Phone Number', 'wp-travel' ),
				'name' => 'wp_travel_phone',
				'id' => 'wp-travel-phone',
				'validations' => array(
					'required' => true,
					'maxlength' => '50',
					'pattern' => '^[\d\+\-\.\(\)\/\s]*$',
				),
				'priority' => 80,
			),
			'country'		=> array(
				'type' => 'select',
				'label' => __( 'Country', 'wp-travel' ),
				'name' => 'wp_travel_country',
				'id' => 'wp-travel-country',
				'options' => wp_travel_get_countries(),
				'validations' => array(
					'required' => true,
				),
				'priority' => 90,
			),
		);

		return $travellers_fileds;
	}
}

new WP_Travel_Checkout();
