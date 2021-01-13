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
		<div class="wti__single-inner">
			<div class="wti__hero-section">
				<img src="<?php echo esc_url( wp_travel_get_post_thumbnail_url( get_the_ID(), 'large' ) ) ?>" alt="">
				<div class="wti__hero-content">
					<div class="wti__container">
						<div class="wti__trip-header">
							<h1 class="wti__trip-title"><?php the_title(); ?></h1>
							<div class="wti__trip-meta">
								<div class="trip__location">
									<?php
										$i = 0;
										$terms = get_the_terms( get_the_id(), 'travel_locations' );										
										if ( is_array( $terms ) && count( $terms ) > 0 ) {
											foreach ( $terms as $term ) {
												if ( $i > 0 ) {
													?>
													 ,
													<?php
												}
												?>
												<span><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></span>
												<?php
												$i++;
											}
										}
									?>
								</div>
							</div>
							<div class="wti__trip-review">
								<?php 
									$average_rating = wp_travel_get_average_rating( get_the_id() ); 									
								?>
								<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); ?>">
									<a>
										<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
											<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>
										</span>
									</a>

								</div>
								<span class="rating-text">(<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); ?>)</span>
							</div>
						</div>
						<div class="wti__top-button">
							<button class="wti__button scroll-spy-button" data-scroll="#gallery">
								<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								viewBox="0 0 430.23 430.23" style="enable-background:new 0 0 430.23 430.23;" xml:space="preserve">

								<path d="M217.875,159.668c-24.237,0-43.886,19.648-43.886,43.886c0,24.237,19.648,43.886,43.886,43.886
									c24.237,0,43.886-19.648,43.886-43.886C261.761,179.316,242.113,159.668,217.875,159.668z M217.875,226.541
									c-12.696,0-22.988-10.292-22.988-22.988c0-12.696,10.292-22.988,22.988-22.988h0c12.696,0,22.988,10.292,22.988,22.988
									C240.863,216.249,230.571,226.541,217.875,226.541z"/>
								<path d="M392.896,59.357L107.639,26.966c-11.071-1.574-22.288,1.658-30.824,8.882c-8.535,6.618-14.006,16.428-15.151,27.167
									l-5.224,42.841H40.243c-22.988,0-40.229,20.375-40.229,43.363V362.9c-0.579,21.921,16.722,40.162,38.644,40.741
									c0.528,0.014,1.057,0.017,1.585,0.01h286.824c22.988,0,43.886-17.763,43.886-40.751v-8.359
									c7.127-1.377,13.888-4.224,19.853-8.359c8.465-7.127,13.885-17.22,15.151-28.212l24.033-212.114
									C432.44,82.815,415.905,62.088,392.896,59.357z M350.055,362.9c0,11.494-11.494,19.853-22.988,19.853H40.243
									c-10.383,0.305-19.047-7.865-19.352-18.248c-0.016-0.535-0.009-1.07,0.021-1.605v-38.661l80.98-59.559
									c9.728-7.469,23.43-6.805,32.392,1.567l56.947,50.155c8.648,7.261,19.534,11.32,30.825,11.494
									c8.828,0.108,17.511-2.243,25.078-6.792l102.922-59.559V362.9z M350.055,236.99l-113.894,66.351
									c-9.78,5.794-22.159,4.745-30.825-2.612l-57.469-50.678c-16.471-14.153-40.545-15.021-57.992-2.09l-68.963,50.155V149.219
									c0-11.494,7.837-22.465,19.331-22.465h286.824c12.28,0.509,22.197,10.201,22.988,22.465V236.99z M409.112,103.035
									c-0.007,0.069-0.013,0.139-0.021,0.208l-24.555,212.114c0.042,5.5-2.466,10.709-6.792,14.106c-2.09,2.09-6.792,3.135-6.792,4.18
									V149.219c-0.825-23.801-20.077-42.824-43.886-43.363H77.337l4.702-40.751c1.02-5.277,3.779-10.059,7.837-13.584
									c4.582-3.168,10.122-4.645,15.674-4.18l284.735,32.914C401.773,81.346,410.203,91.545,409.112,103.035z"/>
								</svg>
								<?php esc_html_e( 'View Photos', 'wp-travel' ); ?>
							</button>
							<button class="wti__button wp-travel-send-enquiries">
								<svg id="Capa_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m219.255 355.16-28.073 28.273c-.363.365-.778.443-1.063.443h-.002c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.93-7.677-2.929-10.606-.001-2.929 2.929-2.929 7.678 0 10.606l10.427 10.429c3.117 3.116 7.259 4.832 11.666 4.832h.029c4.418-.008 8.566-1.738 11.681-4.874l28.074-28.274c2.918-2.939 2.901-7.688-.038-10.606-2.941-2.918-7.688-2.901-10.607.038zm10.607-204.23c-2.94-2.919-7.688-2.902-10.607.038l-28.073 28.273c-.363.365-.778.442-1.062.442h-.003c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.929-7.677-2.93-10.606-.001s-2.929 7.678 0 10.606l10.427 10.429c3.117 3.117 7.259 4.832 11.667 4.832h.03c4.418-.008 8.566-1.738 11.68-4.874l28.074-28.273c2.917-2.939 2.9-7.687-.039-10.606zm0 102.096c-2.94-2.918-7.688-2.902-10.607.038l-28.073 28.273c-.363.365-.778.442-1.062.442h-.003c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.93-7.677-2.929-10.606-.001-2.929 2.929-2.929 7.678 0 10.606l10.427 10.429c3.117 3.117 7.259 4.832 11.667 4.832h.03c4.418-.008 8.566-1.738 11.68-4.874l28.074-28.273c2.917-2.939 2.9-7.687-.039-10.606zm-47.013-110.481h-24.446c-9.098 0-16.5 7.402-16.5 16.5v54.097c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.978c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.978c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.097c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5zm230.011-127.145h-34.38c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5h34.38c12.341 0 22.38 10.04 22.38 22.38v421.84c0 12.34-10.04 22.38-22.38 22.38h-313.72c-12.341 0-22.38-10.04-22.38-22.38v-421.84c0-12.34 10.04-22.38 22.38-22.38h56.803v14.88h-51.823c-6.881 0-12.48 5.599-12.48 12.479v308.854c0 4.143 3.358 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-306.333h35.637c-.821 2.33-1.273 4.832-1.273 7.439v20.802c0 12.374 10.066 22.44 22.439 22.44h185.113c12.373 0 22.439-10.066 22.439-22.44v-20.801c0-2.607-.452-5.11-1.273-7.439h35.637v328.83h-38.919c-20.61 0-37.378 16.77-37.378 37.383v38.927h-222.422v-68.807c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v71.327c0 6.881 5.599 12.479 12.48 12.479h232.44c1.994.003 3.9-.803 5.304-2.197l.018-.018 76.282-76.292c1.276-1.284 2.057-3.018 2.173-4.824.004-.067.024-339.16.024-339.33 0-6.881-5.599-12.479-12.48-12.479h-51.823v-20.807c-.001-13.494-10.978-24.473-24.471-24.473h-151.174c-10.288 0-19.107 6.386-22.719 15.4h-58.554c-20.612 0-37.38 16.769-37.38 37.38v421.84c0 20.611 16.769 37.38 37.38 37.38h313.72c20.612 0 37.38-16.769 37.38-37.38v-421.84c0-20.611-16.768-37.38-37.38-37.38zm-68.797 439.409v-28.315c0-12.342 10.039-22.383 22.378-22.383h28.313zm-173.12-430.336c0-5.224 4.248-9.473 9.47-9.473h151.174c5.222 0 9.47 4.249 9.47 9.473v27.521c0 5.224-4.248 9.473-9.47 9.473h-151.174c-5.222 0-9.47-4.249-9.47-9.473zm-14.939 43.247c0-2.091.869-3.98 2.262-5.333 3.915 8.311 12.368 14.079 22.147 14.079h151.174c9.778 0 18.232-5.768 22.147-14.079 1.393 1.353 2.262 3.242 2.262 5.333v20.802c0 4.103-3.337 7.44-7.439 7.44h-185.114c-4.102 0-7.439-3.338-7.439-7.44zm-14.101 247.518c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.978c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.978c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.097c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-24.446c-9.098 0-16.5 7.402-16.5 16.5zm113.722 46.5h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm-113.722 55.596c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.977c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.977c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.096c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-24.446c-9.098 0-16.5 7.402-16.5 16.5zm113.722-25.596h61.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-61.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-102.096h61.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-61.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-30h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-72.097h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-30h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5z"/></g></svg>
								<?php esc_html_e( 'Trip Enquiry', 'wp-travel' ); ?>
							</button>
								<!-- <a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
									<span class="wp-travel-booking-enquiry">
										<span class="dashicons dashicons-editor-help"></span>
										<span>
											Trip Enquiry						</span>
									</span>
								</a> -->
							<div class="trip-code">
								<?php
								$strings         = wp_travel_get_strings();
								$trip_code_label = $strings['trip_code'];
								echo esc_html( $trip_code_label ); ?> : <span><?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
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
