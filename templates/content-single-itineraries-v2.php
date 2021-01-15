<?php
/**
 * Itinerary Single Contnet Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-single-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_travel_itinerary;
?>

<?php
do_action( 'wp_travel_before_single_itinerary', get_the_ID() );
if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
$wrapper_class = wp_travel_get_theme_wrapper_class();
do_action( 'wp_travel_before_content_start');
?>

<div id="wti_main-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="wti__wrapper">
		<div class="wti__single-inner <?php echo esc_attr( $wrapper_class ); ?>">
			<?php
				/**
				 * Hook 'wp_travel_itinerary_v2_hero_section'.
				 *
				 * @hooked 'wp_travel_hero_section'.
				 * @param int get_the_ID().
				 */
				do_action( 'wp_travel_itinerary_v2_hero_section', get_the_ID() ); 
			?>
			<div class="wti__single-wrapper">
				<div class="wti__nav-tabs">
					<div class="wti__container">
						<div class="wti__grid">
							<div class="wti__grid-item col-lg-8">
								<div id="scrollspy-buttons" class="wti__scrollspy-buttons">
								<span class="line"></span>
								<?php 
								$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();
								$index = 1;
								if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) {
									foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) {
										?>
										<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
											<?php continue; ?>
										<?php endif; ?>
										<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
											<?php continue; ?>
										<?php endif; ?>
										<?php
										$tab_label = $tab_info['label'];
										if ( 'booking' === $tab_key ) {
											continue;
										}
										?>
										<button class="scroll-spy-button <?php echo esc_attr( $tab_key ); ?> <?php echo esc_attr( $tab_info['label_class'] ); ?>" data-scroll='#<?php echo esc_attr( $tab_key ); ?>'>
											<?php echo esc_attr( $tab_label ); ?>
										</button>
										<?php
										$index++;
									}
								}
								?>
								</div>
							</div>
							<div class="wti__grid-item col-lg-4">
								<div class="wti__single-price-area">
									<div class="price-amount">
										<?php
										$trip_id = get_the_id();
										$strings = wp_travel_get_strings();
										$args = $args_regular = array( 'trip_id' => $trip_id );
										$args_regular['is_regular_price'] = true;
										$trip_price= WP_Travel_Helpers_Pricings::get_price( $args );
										$regular_price= WP_Travel_Helpers_Pricings::get_price( $args_regular );
										$enable_sale   = WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id, 'from_price_sale_enable' => true ) );
										?>
										<span class="price-from"><?php echo esc_html( $strings['from'] ); ?>: </span>
										<strong class="price-figure">
											<!-- <span class="curruncy"> -->
												<?php if ( $enable_sale ) : ?>
												<del>
													<span><?php echo wp_travel_get_formated_price_currency( $regular_price, true ); ?></span>
												</del>
												<?php endif; ?>
												<span class="person-count">
													<ins>
														<span><?php echo wp_travel_get_formated_price_currency( $trip_price ); ?></span>
													</ins>
												</span>
											<!-- </span> -->
										</strong>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="wti__content-wrapper">
					<div class="wti__container">
						<div class="wti__grid">
							<div class="wti__grid-item col-lg-8">
								<div class="wti__tab-content-area">
									<?php
									$settings = wp_travel_get_settings();

									if ( empty( $settings['wp_travel_trip_facts_settings'] ) ) {
										return '';
									}
									
									$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';
								
									if ( 'no' === $wp_travel_trip_facts_enable ) {
										return;
									}
								
									$wp_travel_trip_facts = get_post_meta( get_the_id(), 'wp_travel_trip_facts', true );
								
									if ( is_string( $wp_travel_trip_facts ) && '' != $wp_travel_trip_facts ) {
								
										$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts, true );
									}
								
									$i = 0;
									?>
									<div class="wti__trip-info">
										<?php
										/**
										 * To fix fact not showing on frontend since v4.0 or greater.
										 *
										 * Modified @since v4.4.1
										 */
										$settings_facts  = $settings['wp_travel_trip_facts_settings'];
										if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {
											foreach ( $wp_travel_trip_facts as $key => $trip_fact ) : ?>
												<?php
												$trip_fact_id   = $trip_fact['fact_id'];	
												if ( isset( $settings_facts[ $trip_fact_id ] ) ) { // To check if current trip facts id matches the settings trip facts id. If matches then get icon and label.

													$icon  = $settings_facts[ $trip_fact_id ]['icon'];
													$label = $settings_facts[ $trip_fact_id ]['name'];
																			
												} else { // If fact id doesn't matches or if trip fact doesn't have fact id then matching the trip fact label with fact setting label. ( For e.g Transports ( fact in trip ) === Transports ( Setting fact option ) )
													$trip_fact_setting = array_filter(
														$settings_facts,
														function( $setting ) use ( $trip_fact ) {
															
															return $setting['name'] === $trip_fact['label']; 
														}
													); // Gives an array for matches label with its other detail as well.
													
													if ( empty( $trip_fact_setting ) ) { // If there is empty array that means label doesn't matches. Hence skip that and continue.
														continue;
													}
													foreach ( $trip_fact_setting as $set ) {
														$icon  = $set['icon'];
														$label = $set['name'];
													}
												}

												if ( isset( $trip_fact['value'] ) ) :
													?>
													<div class="wti__trip-info-item">

														<div class="trip__info-icon">
															<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
															<strong><?php echo esc_html( $label ); ?></strong>:
														</div>
														<!-- <i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i> -->
														<?php
														if ( $trip_fact['type'] === 'multiple' ) {
															$count = count( $trip_fact['value'] );
															$i     = 1;
															foreach ( $trip_fact['value'] as $key => $val ) {
																// echo esc_html( $val );
																if ( isset( $trip_fact['fact_id'] ) ) {
																	?>
																	<span class="trip__info-label">
																		<?php echo @esc_html( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $val ] ); ?>
																	</span>
																	<?php
																} else {
																	?>
																	<span class="trip__info-label">
																		<?php echo esc_html( $val ); ?>
																	</span>
																	<?php
																}
																if ( $count > 1 && $i !== $count ) {
																	?>
																	<span class="trip__info-label">
																		<?php echo esc_html( ',', 'wp-travel' ); ?>
																	</span>
																	<?php
																}
																$i++;
															}
														} elseif ( isset( $trip_fact['fact_id'] ) && 'single' === $trip_fact['type'] ) {
															?>
															<span class="trip__info-label">
																<?php echo esc_html( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $trip_fact['value'] ] ); ?>
															</span>
															<?php
														} else {
															?>
															<span class="trip__info-label">
																<?php echo esc_html( $trip_fact['value'] ); ?>
															</span>
															<?php
														}
														?>
													</div>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php } ?>
									</div>
									<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
										<?php $index = 1; ?>
										<?php foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) : ?>
											<?php $tab_label = $tab_info['label']; ?>
											<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
												<?php continue; ?>
											<?php endif; ?>
											<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
												<?php continue; ?>
											<?php endif; ?>
											<?php
											switch ( $tab_key ) {

												case 'reviews':
													?>
													<div id="<?php echo esc_attr( $tab_key ); ?>" class="wti__tab-content-wrapper">
														<h3 class="tab-content-title"><?php echo esc_attr( $tab_label ); ?></h3>
														<?php comments_template(); ?>
													</div>
													<?php
													break;
												case 'booking':
													continue 2;
												case 'faq':
													?>
													<div id="<?php echo esc_attr( $tab_key ); ?>" class="trip-faq wti__tab-content-wrapper">
														<h3 class="tab-content-title"><?php echo esc_attr( $tab_label ); ?></h3>
														<div class="accordion" id="accordion">
															<?php
															$faqs = wp_travel_get_faqs( get_the_id() );
															if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
																?>
																<!-- <div class="wp-collapse-open clearfix">
																	<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
																	<a href="#" class="close-all-link" style="display:none;"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
																</div> -->
																<?php foreach ( $faqs as $k => $faq ) : ?>
																<!-- New -->
																	<div class="accordion-panel">
																		<div class="accordion-panel-heading">
																			<h4 class="accordion-panel-title">
																				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" class="svg-inline--fa fa-caret-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>
																				
																				<p class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k + 1 ); ?>" aria-expanded="true">
																				<?php echo esc_html( $faq['question'] ); ?>                
																				<span class="collapse-icon"></span>
																				</p>
																			</h4>
																		</div>
																		<div id="collapse<?php echo esc_attr( $k + 1 ); ?>" class="accordion-panel-collapse " aria-expanded="true" >
																			<div class="panel-body">
																				<?php echo wp_kses_post( wpautop( $faq['answer'] ) ); ?>
																			</div>
																		</div>
																	</div>
																	<?php
																endforeach;
															} else {
																?>
																<div class="while-empty">
																	<p class="wp-travel-no-detail-found-msg" >
																		<?php esc_html_e( 'No Details Found', 'wp-travel' ); ?>
																	</p>
																</div>
															<?php } ?>
														</div>
													</div>
													<?php
													break;
												case 'trip_outline':
													?>
												<div id="<?php echo esc_attr( $tab_key ); ?>" class="wti__tab-content-wrapper">
													<h3 class="tab-content-title"><?php echo esc_attr( $tab_label ); ?></h3>
													<div class="trip-itinerary__wrapper">
													<?php
														// echo wp_kses_post( $tab_info['content'] );

														// $itinerary_list_template = wp_travel_get_template( 'itineraries-list.php' );
														// load_template( $itinerary_list_template );
													
													global $post;
													$post_id     = $post->ID;
													$itineraries = get_post_meta( $post_id, 'wp_travel_trip_itinerary_data' );
													if ( isset( $itineraries[0] ) && ! empty( $itineraries[0] ) ) : ?>
															<?php $index = 1; ?>
															<?php foreach ( $itineraries[0] as $key => $itinerary ) : ?>
																<?php if ( $index % 2 === 0 ) : ?>
																	<?php
																		$first_class  = 'right';
																		$second_class = 'left';
																		$row_reverse  = 'row-reverse';
																	?>
																<?php else : ?>
																	<?php
																		$first_class  = 'left';
																		$second_class = 'right';
																		$row_reverse  = '';
																	?>
																<?php endif; ?>
																<?php

																$date_format = get_option( 'date_format' );
																$time_format = get_option( 'time_format' );

																$itinerary_label = '';
																$itinerary_title = '';
																$itinerary_desc  = '';
																$itinerary_date  = '';
																$itinerary_time  = '';
																if ( isset( $itinerary['label'] ) && '' !== $itinerary['label'] ) {
																	$itinerary_label = stripslashes( $itinerary['label'] );
																}
																if ( isset( $itinerary['title'] ) && '' !== $itinerary['title'] ) {
																	$itinerary_title = stripslashes( $itinerary['title'] );
																}
																if ( isset( $itinerary['desc'] ) && '' !== $itinerary['desc'] ) {
																	$itinerary_desc = stripslashes( $itinerary['desc'] );
																}
																if ( isset( $itinerary['date'] ) && '' !== $itinerary['date'] ) {
																	$itinerary_date = wp_travel_format_date( $itinerary['date'] );
																}
																if ( isset( $itinerary['time'] ) && '' !== $itinerary['time'] ) {
																	$itinerary_time = stripslashes( $itinerary['time'] );
																	$itinerary_time = date( $time_format, strtotime( $itinerary_time ) );
																}
																?>
																<div class="trip-itinerary__item">
																	<h5 class="trip-itinerary__title">
																		<strong>
																			<?php
																				if ( '' !== $itinerary_label ) {
																					echo esc_html( $itinerary_label ) . ':' ;
																				}
																				if ( $itinerary_date ) {
																					echo esc_html_e( 'Date', 'wp-travel' ). ':' . esc_html( $itinerary_date );
																				}
																				if ( $itinerary_time ) {
																					echo esc_html_e( 'Time', 'wp-travel' ). ':' . esc_html( $itinerary_time );
																				}
																			?>
																		</strong> 
																		<?php 
																			if ( '' !== $itinerary_title ) {
																				echo esc_html( $itinerary_title );
																			}
																			do_action( 'wp_travel_itineraries_after_title', $itinerary );
																		?>
																	</h5>
																	<?php echo wp_kses_post( $itinerary_desc ); ?>
																</div>
																<?php $index++; ?>
															<?php endforeach; ?>
													<?php endif; ?>

													</div>
												</div>
													<?php
													break;
												default:
													?>
													<div id="<?php echo esc_attr( $tab_key ); ?>" class="wti__tab-content-wrapper">
														<h3 class="tab-content-title"><?php echo esc_attr( $tab_label ); ?></h3>
														<?php
														if ( apply_filters( 'wp_travel_trip_tabs_output_raw', false, $tab_key ) ) {

															echo do_shortcode( $tab_info['content'] );

														} else {

															echo apply_filters( 'the_content', $tab_info['content'] );
														}

														?>
													</div>
												<?php break; ?>
											<?php } ?>
											<?php
											$index++;
										endforeach;
										?>
									<?php endif; ?>
								</div>
							</div>
							<div class="wti__grid-item col-lg-4">
								<div class="wti__booking-area">
									<div class="wti__booking">
										<!-- <div class="wti__booking-date-picker">
											<input type="date" class="wti-booking-date-picker">
										</div> -->
										<?php
											$booking_template = wp_travel_get_template( 'content-pricing-options.php' );
											load_template( $booking_template );
										?>
										<!-- <div class="wti__selectors">
											<div class="wti__selector-item wti__pax-selector active">
												<h5 class="wti__selector-heading">
													Pax Selector 
													<div class="buttons">
														<span class="toggler-icon"><i class="fas fa-chevron-down"></i></span>
													</div>
												</h5>
												<div class="wti__selector-content-wrapper">
													<div class="wti__selector-inner">
														<div class="wti__selector-option">
															<h6 class="wti__selector-option-title">Adults</h6>
															<div class="wti__selector-people-input">
																<div class="input-field">
																	<button type="button" class="decrease_val">-</button>
																	<input type="number" min="0" max="5" value="0">
																	<button type="button" class="increase_val">+</button>
																</div>
																<div class="wti__input-display-figure">
																	<h6>2 X $200.200</h6>
																</div>
															</div>
														</div>
														<div class="wti__selector-option">
															<h6 class="wti__selector-option-title">Childrens</h6>
															<div class="wti__selector-people-input">
																<div class="input-field">
																	<button type="button" class="decrease_val">-</button>
																	<input type="number" min="0" max="5" value="0">
																	<button type="button" class="increase_val">+</button>
																</div>
																<div class="wti__input-display-figure">
																	<h6>2 X $200.200</h6>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="wti__selector-item wti__trip-extras">
												<h5 class="wti__selector-heading">
													Trip Extras
													<div class="buttons">
														<span class="toggler-icon"><i class="fas fa-chevron-down"></i></span>
													</div>
												</h5>
												<div class="wti__selector-content-wrapper">
													<div class="wti__selector-inner">
														<div class="wti__selector-option">
															<h6 class="wti__selector-option-title">Water Bottle (s) &nbsp; | &nbsp;<a href="#">Read More »</a></h6>
															<div class="wti__selector-people-input">
																<div class="input-field">
																	<button type="button" class="decrease_val">-</button>
																	<input type="number" min="0" max="5" value="0">
																	<button type="button" class="increase_val">+</button>
																</div>
																<div class="wti__input-display-figure">
																	<h6>2 X $200.200</h6>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="wti__booking-amounts">
											<div class="wti__amount amount-of-pax">
												<h5>Pax Amount</h5>
												<div class="amount-figure">
													<span>$400.00 + $150.00</span>
													<strong class="total">$550.00</strong>
												</div>
											</div>
											<div class="wti__amount amount-of-pax">
												<h5>Trip Extras Amount</h5>
												<div class="amount-figure">
													<span>$00.00</span>
													<strong class="total">$00.00</strong>
												</div>
											</div>
											<div class="wti__amount amount-of-pax">
												<h5>Discount</h5>
												<div class="amount-figure">
													<span>15%</span>
													<strong class="total wti__discount-amount">-$82.50</strong>
												</div>
											</div>
										</div>
										<div class="wti__booking-total-amount">
											<h3 class="amount-figure">
												<span>Total Cost</span>
												<strong class="total-amount">$550.00</strong>
											</h3>
										</div>
										<button class="wti__book-now-button">
											Book Now
										</button> -->
									</div>
								</div>
							</div>
							<!-- Related Trips -->
							<?php
							$post_id = get_the_id();
							$settings = wp_travel_get_settings();
							$hide_related_itinerary = ( isset( $settings['hide_related_itinerary'] ) && '' !== $settings['hide_related_itinerary'] ) ? $settings['hide_related_itinerary'] : 'no';
							
							if ( 'yes' === $hide_related_itinerary ) {
								return;
							}
							$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
							$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
						
							// For use in the loop, list 5 post titles related to first tag on current post.
							$terms = wp_get_object_terms( $post_id, 'itinerary_types' );
						
							$no_related_post_message = '<p class="wp-travel-no-detail-found-msg">' . esc_html__( 'Related trip not found.', 'wp-travel' ) . '</p>';
							$wrapper_class = wp_travel_get_theme_wrapper_class();
							?>
							<div class="wti__grid-item col-12">
								<div class="wti__related-trips">
									<hr class="wti__trip-section-devider">
									<h3 class="related-trip-title"><?php echo apply_filters( 'wp_travel_related_post_title', esc_html__( 'Related Trips', 'wp-travel' ) ); ?></h3>
									<div class="wti__list-wrapper">
										<div class="wti__list">
										<?php
										if ( ! empty( $terms ) ) {
											$term_ids = wp_list_pluck( $terms, 'term_id' );
											$col_per_row = apply_filters( 'wp_travel_related_itineraries_col_per_row' , '3' );
											$args = array(
												'post_type' => WP_TRAVEL_POST_TYPE,
												'post__not_in' => array( $post_id ),
												'posts_per_page' => $col_per_row,
												'tax_query' => array(
													array(
														'taxonomy' => 'itinerary_types',
														'field' => 'id',
														'terms' => $term_ids,
													),
												),
											);
											$query = new WP_Query( $args );
											if ( $query->have_posts() ) { ?>
												<?php while ( $query->have_posts() ) : $query->the_post(); ?>
													<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item-new' ); ?>
												<?php endwhile; ?>
											<?php
											} else {
												wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
											}
											wp_reset_query();
										} else {
											wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' );
										}
										?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- wti__single-inner -->
	</div><!-- wti__wrapper -->
</div><!-- #wti_main-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary', get_the_ID() ); ?>
