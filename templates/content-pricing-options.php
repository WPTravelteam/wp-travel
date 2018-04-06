<?php 
/**
 * Itinerary Pricing Options Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-pricing-options.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.1.5
 */
global $post;
global $wp_travel_itinerary;

$post_id = $post->ID;

	$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
	$trip_content	= $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
	$trip_outline	= $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
	$trip_include	= $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
	$trip_exclude	= $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
	$gallery_ids 	= $wp_travel_itinerary->get_gallery_ids();

	$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();

	$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );

	$trip_start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$trip_end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );
	$trip_price      = wp_travel_get_trip_price( $post_id );
	$enable_sale     = get_post_meta( $post_id, 'wp_travel_enable_sale', true );

	$trip_duration = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$settings = wp_travel_get_settings();
	$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	$per_person_text = wp_travel_get_price_per_text( $post_id );
	$sale_price      = wp_travel_get_trip_sale_price( $post_id ); 

	$wp_travel_enable_pricing_options = get_post_meta( $post_id, 'wp_travel_enable_pricing_options', true );
?>
<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">

<?php 
$enable_checkout = apply_filters( 'wp_travel_enable_checkout', true );
if ( $enable_checkout && wp_travel_is_payment_enabled()) : 

	if ( 'yes' === $wp_travel_enable_pricing_options ) :

?>
		<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list">
					<li class="availabily-heading clearfix">
							<div class="date-from">
								<?php echo esc_html( 'Pricing Name', 'wp-travel' ); ?>
							</div>
						<div class="status">
							<?php echo esc_html( 'Min Group Size', 'wp-travel' ); ?>
						</div>
						<div class="status">
							<?php echo esc_html( 'Max Group Size', 'wp-travel' ); ?>
						</div>
						<div class="price">
							<?php echo esc_html( 'Price', 'wp-travel' ); ?>
						</div>
						<div class="action">
							&nbsp;
						</div>
					</li>
					<li class="availabily-content clearfix">
						<div class="date-from">
							<span class="availabily-heading-label"><?php echo esc_html( 'Pricing Name:', 'wp-travel' ); ?></span> <span>Christmas Special</span>
						</div>
						<div class="status">
							<span class="availabily-heading-label"><?php echo esc_html( 'Min Group Size:', 'wp-travel' ); ?></span>
							<span>2 pax</span>
						</div>
						<div class="status">
							<span class="availabily-heading-label"><?php echo esc_html( 'Max Group Size:', 'wp-travel' ); ?></span>
							<span>20 pax</span>
						</div>
						<div class="price">
							<span class="availabily-heading-label"><?php echo esc_html( 'Price:', 'wp-travel' ); ?></span>
							<del>
								<span> $ 400 </span>
							</del>
							<span class="person-count">
								<ins><span>$ 300</span>
								</ins>/Person 
							</span>
						</div>
						<div class="action">
							<a href="#0" class="btn btn-primary btn-sm btn-inverse show-booking-row">Select</a> 
						</div>
						<div class="wp-travel-booking-row">
							<div class="wp-travel-calender-column no-padding ">
								<label for="">Select a Date:</label>
								<span id="few-dates-enable">
								</span>
							</div>
							<div class="wp-travel-calender-aside">
								<div class="col-sm-6">
									<label for="">Adult:</label>
									<input type="number" name="" placeholder="Size">
								</div>
								<div class="col-sm-6">
									<label for="">Infant:</label>
									<input type="number" name="" placeholder="Size">
								</div>
								<div class="col-sm-6">
									<label for="">Group Size:</label>
									<input type="number" name="" placeholder="Size">
								</div>
								<div class="add-to-cart">
									<a href="http://skynet.wensolutions.com/travel-log/wp-travel-cart/?trip_id=777" class="btn btn-primary btn-sm btn-inverse">Book now</a>
								</div>
							</div>
						</div>
					</li>
					<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/css/datepicker.min.css">
					<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/js/datepicker.min.js"></script>
					<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/js/i18n/datepicker.en.min.js"></script>

					<script type="text/javascript">
						jQuery('.wp-travel-booking-row').hide();
						jQuery('.show-booking-row').click(function(){
							jQuery(this).parent('.action').siblings('.wp-travel-booking-row').toggle('fast').addClass('animate');
							jQuery(this).text(function(i, text){
								return text === "Select" ? "Close" : "Select";
							})
						});

						var disabledDays = [0,1,2,3,4,5,6];

						var enableDays = [3];


						var eventDates = [1, 10, 12, 22],
					    $picker = jQuery('#few-dates-enable'),
					    sold = [2018-04-5, 2018-04-10, 2018-04-15, 2018-04-20],
						avalible = [2018-04-12, 2018-04-18, 2018-04-25, 2018-04-30],
						notenough = new Array();

						 $picker.datepicker({
						 	language: 'en',
						 	todayButton:true,
							onRenderCell: function (d, type) {
								if (type == 'day') {
									var cellYear = d.getFullYear(),
				                    cellMonth = d.getMonth(),
				                    cellDate = d.getDate(),
				                    disabled = true,
				                    classes = false,
				                    html = false,
				                    year, month, date;
					                if (cellDate == date) {
					                    disabled= true;
					                }
					                avalible.forEach(function (avalible) {
					                    avalible = avalible.split('-');
					                    year = avalible[0];
					                    month = parseInt(avalible[1]) - 1;
					                    date = parseInt(avalible[2]);

					                    if (cellYear == year && cellMonth == month && cellDate == date) {
					                        classes = "avalible";
					                        disabled= false;
					                 
					                    }
					                });
					                notenough.forEach(function (notenough) {
					                    notenough = notenough.split('-');
					                    year = notenough[0];
					                    month = parseInt(notenough[1]) - 1;
					                    date = parseInt(notenough[2]);

					                    if (cellYear == year && cellMonth == month && cellDate == date) {
					                        classes = "notenough",
					                            disabled= true;
					                    }
					                });
					                sold.forEach(function (sold) {
					                    sold = sold.split('-');
					                    year = sold[0];
					                    month = parseInt(sold[1]) - 1;
					                    date = parseInt(sold[2]);

					                    if (cellYear == year && cellMonth == month && cellDate == date) {
					                        classes = "sold";
					                        disabled= true;
					                    }
					                });
					                return {
					                    classes: classes,
					                    disabled: disabled,
					       
					                }
					            }    
							}
						 });
					</script>
				</ul>
			</div>
		</div>
	<?php else : ?>
	<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list">
					<li class="availabily-heading clearfix">
						<?php if ( 'yes' == $fixed_departure ) : ?>
							<div class="date-from">
								<?php echo esc_html( 'Start', 'wp-travel' ); ?>
							</div>
							<div class="date-to">
								<?php echo esc_html( 'End', 'wp-travel' ); ?>
							</div>
						<?php else :?>
							<div class="date-from">
								<?php echo esc_html( 'Trip Duration', 'wp-travel' ); ?>
							</div>
						<?php endif; ?>

						<div class="status">
							<?php echo esc_html( 'Group Size', 'wp-travel' ); ?>
						</div>
						<div class="price">
							<?php echo esc_html( 'Price', 'wp-travel' ); ?>
						</div>
						<div class="action">
							&nbsp;
						</div>
					</li>
					<li class="availabily-content clearfix">
						<?php if ( 'yes' == $fixed_departure ) : ?>
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html( 'start:', 'wp-travel' ); ?></span>
								<?php echo esc_html( date( 'l', strtotime( $trip_start_date ) ) );  ?>
								<?php $date_format = get_option( 'date_format' ); ?>
								<?php if ( ! $date_format ) : ?>
									<?php $date_format = 'jS M, Y'; ?>
								<?php endif; ?>
								<span><?php echo esc_html( date( $date_format, strtotime( $trip_start_date ) ) );  ?></span>
							</div>
							<div class="date-to">
								<span class="availabily-heading-label"><?php echo esc_html( 'end:', 'wp-travel' ); ?></span>
								<?php echo esc_html( date( 'l', strtotime( $trip_end_date ) ) );  ?>
								<span><?php echo esc_html( date( $date_format, strtotime( $trip_end_date ) ) );  ?></span>
							</div>
						<?php else : ?>
							<div class="date-from">
								<span><?php esc_html_e( 'Day(s):', 'wp-travel' ); ?> <?php echo esc_html( $trip_duration ); ?> </span>

								<span><?php esc_html_e( 'Night(s):', 'wp-travel' ); ?> <?php echo esc_html( $trip_duration_night ); ?> </span>
							</div>
						<?php endif; ?>
						<div class="status">
							<span class="availabily-heading-label"><?php echo esc_html( 'Group Size:', 'wp-travel' ); ?></span>
							<span><?php echo esc_html( wp_travel_get_group_size() ); ?></span>
						</div>
						<div class="price">
							<span class="availabily-heading-label"><?php echo esc_html( 'price:', 'wp-travel' ); ?></span>
							<?php if ( '' != $trip_price || '0' != $trip_price ) : ?>

								<?php if ( $enable_sale ) : ?>
									<del>
										<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
									</del>
								<?php endif; ?>
									<span class="person-count">
										<ins>
											<span>
												<?php
												if ( $enable_sale ) {
													echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price );
												} else {
													echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price );
												}
												?>
											</span>
										</ins>/<?php echo esc_html( $per_person_text ); ?>
									</span>
							<?php endif; ?>
						</div>
						<div class="action">	
						<?php 
							$button = '<a href="%s" class="btn btn-primary btn-sm btn-inverse">%s</a>';
							$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
							if ( 'yes' !== $fixed_departure ) :
								$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url );
							endif;
							printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
						?>
						</div>
					</li>
				</ul>
			</div>
		</div>

	<?php endif; ?>
<?php else : ?>
	<?php echo wp_travel_get_booking_form(); ?>
<?php endif; ?>
</div>