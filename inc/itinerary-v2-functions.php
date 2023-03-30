<?php
/**
 * Itinerary v2 Template Functions.
 *
 * @package WP_Travel
 */

// Hooks.

add_action( 'wp_travel_itinerary_v2_hero_section', 'wptravel_hero_section' );
add_action( 'wp_travel_itinerary_v2_hero_banner_image', 'wptravel_hero_section_banner_image' );
add_action( 'wp_travel_single_itinerary_trip_location', 'wptravel_single_trip_location' );
add_action( 'wp_travel_single_itinerary_trip_review', 'wptravel_single_trip_review' );
add_action( 'wp_travel_before_single_trip_code', 'wptravel_single_trip_buttons' );
add_action( 'wp_travel_single_trip_code', 'wptravel_single_itinerary_trip_code' );
add_action( 'wp_travel_single_trip_facts', 'wptravel_single_itinerary_trip_facts' );
add_action( 'wp_travel_before_single_trip_main_contents', 'wptravel_single_itinerary_maps' );
add_action( 'wp_travel_single_trip_main_contents', 'wptravel_single_itinerary_main_contents' );
add_action( 'wp_travel_single_itinerary_related_trips', 'wptravel_single_related_trips' );
add_action( 'wp_travel_single_trip_meta_information', 'wptravel_single_itinerary_meta_info' );
add_action( 'wp_travel_archive_v2_listing_sidebar', 'wptravel_archive_v2_listing_sidebar' );

/**
 * Main hero section for itinerary page.
 *
 * @param int $trip_id Trip ID.
 * @return void.
 */
function wptravel_hero_section( $trip_id ) {

	global $wp_travel_itinerary;
	?>
	<div class="wti__hero-section">
		<?php
			/**
			 * Hook 'wp_travel_itinerary_v2_hero_banner_image'.
			 *
			 * @hooked 'wp_travel_hero_section_banner_image'.
			 * @param int $trip_id.
			 */
			do_action( 'wp_travel_itinerary_v2_hero_banner_image', $trip_id );
		?>
		<div class="wti__hero-content">
			<div class="wti__container">
				<div class="wti__trip-header">
					<div class="wti__trip-title-wrapper">
						<?php
						/**
						 * Hook 'wp_travel_before_single_title'.
						 *
						 * @param int trip_id.
						 */
						do_action( 'wp_travel_before_single_title', get_the_ID() );
						wptravel_do_deprecated_action( 'wp_tarvel_before_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_single_title' );
						$show_title = apply_filters( 'wp_travel_show_single_page_title', true );

						if ( $show_title ) {
							the_title( '<h1 class="wti__trip-title">', '</h1>' );
						}
						?>
					</div>
					<?php
					/**
					 * Hook 'wp_travel_single_trip_after_title'.
					 *
					 * @param int trip_id.
					 */
					do_action( 'wp_travel_single_trip_after_title', get_the_ID() );
					wptravel_do_deprecated_action( 'wp_travel_after_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_single_trip_after_title' );  // @since 1.0.4 and deprecated in 2.0.4

					/**
					 * Hook 'wp_travel_single_itinerary_trip_location'.
					 *
					 * @hooked 'wp_travel_single_trip_location'.
					 * @param int $trip_id.
					 */
					do_action( 'wp_travel_single_itinerary_trip_location', $trip_id );

					/**
					 * Hook 'wp_travel_single_itinerary_trip_review'.
					 *
					 * @hooked 'wp_travel_single_trip_review'.
					 * @param int $trip_id.
					 */
					do_action( 'wp_travel_single_itinerary_trip_review', $trip_id );
					?>
				</div>
				<div class="wti__top-button">
					<?php
					/**
					 * Hook 'wp_travel_before_single_trip_code'.
					 *
					 * @hooked 'wp_travel_single_trip_buttons'.
					 */
					do_action( 'wp_travel_before_single_trip_code', $trip_id );

					/**
					 * Hook 'wp_travel_single_trip_code'.
					 *
					 * @hooked 'wp_travel_single_itinerary_trip_code'.
					 */
					do_action( 'wp_travel_single_trip_code', $wp_travel_itinerary );
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Main banner for itinerary page.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_hero_section_banner_image( $trip_id ) {
	?>
	<img src="<?php echo esc_url( wptravel_get_post_thumbnail_url( $trip_id, 'large' ) ); ?>" alt="">
	<?php
	if ( WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id ) ) ) {
		?>
		<div class="itinerary-single-trip-offer">
			<span class="wti__trip-meta-offer"><?php esc_html_e( 'Offer', 'wp-travel' ); ?></span>
		</div>
		<?php
	}
}

/**
 * Single trip locations.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_trip_location( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	$terms = get_the_terms( $trip_id, 'travel_locations' );

	if ( ! $terms ) {
		return;
	}
	?>
	<div class="wti__trip-meta">
		<div class="trip__location">
		<?php
		$i = 0;
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
	<?php
}

/**
 * Single trip review section.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_trip_review( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	?>
	<div class="wti__trip-review">
		<?php
			$average_rating = wptravel_get_average_rating( $trip_id );
		?>
		<div class="wp-travel-average-review" title="<?php printf( esc_attr__( 'Rated %s out of 5', 'wp-travel' ), $average_rating ); //phpcs:ignore ?>">
			<a>
				<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
					<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); //phpcs:ignore ?>
				</span>
			</a>

		</div>
		<span class="rating-text">(<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); //phpcs:ignore ?>)</span>
	</div>
	<?php
}

/**
 * Gallery and trip enquiry button section.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_trip_buttons( $trip_id ) {
	$settings = wptravel_get_settings();
	$strings = array();
	if ( class_exists( 'WpTravel_Helpers_Strings' ) ) {
		$string = WpTravel_Helpers_Strings::get(); 
	}
	$strings = isset( $string['trip_enquiry'] ) ? $string['trip_enquiry'] : apply_filters( 'wp_travel_trip_enquiry_labels', __( 'Trip Enquiry', 'wp-travel' ) ) ;

	$enquery_global_setting = isset( $settings['enable_trip_enquiry_option'] ) ? $settings['enable_trip_enquiry_option'] : 'yes';
	$global_enquiry_option  = get_post_meta( $trip_id, 'wp_travel_use_global_trip_enquiry_option', true );

	if ( '' === $global_enquiry_option ) {
		$global_enquiry_option = 'yes';
	}
	if ( 'yes' === $global_enquiry_option ) {

		$enable_enquiry = $enquery_global_setting;

	} else {
		$enable_enquiry = get_post_meta( $trip_id, 'wp_travel_enable_trip_enquiry_option', true );
	}
	?>
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
	<?php if ( 'yes' === $enable_enquiry ) : ?>
		<a class="wti-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
			<button class="wti__button">
				<svg id="Capa_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m219.255 355.16-28.073 28.273c-.363.365-.778.443-1.063.443h-.002c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.93-7.677-2.929-10.606-.001-2.929 2.929-2.929 7.678 0 10.606l10.427 10.429c3.117 3.116 7.259 4.832 11.666 4.832h.029c4.418-.008 8.566-1.738 11.681-4.874l28.074-28.274c2.918-2.939 2.901-7.688-.038-10.606-2.941-2.918-7.688-2.901-10.607.038zm10.607-204.23c-2.94-2.919-7.688-2.902-10.607.038l-28.073 28.273c-.363.365-.778.442-1.062.442h-.003c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.929-7.677-2.93-10.606-.001s-2.929 7.678 0 10.606l10.427 10.429c3.117 3.117 7.259 4.832 11.667 4.832h.03c4.418-.008 8.566-1.738 11.68-4.874l28.074-28.273c2.917-2.939 2.9-7.687-.039-10.606zm0 102.096c-2.94-2.918-7.688-2.902-10.607.038l-28.073 28.273c-.363.365-.778.442-1.062.442h-.003c-.284 0-.698-.076-1.061-.438l-10.427-10.428c-2.928-2.93-7.677-2.929-10.606-.001-2.929 2.929-2.929 7.678 0 10.606l10.427 10.429c3.117 3.117 7.259 4.832 11.667 4.832h.03c4.418-.008 8.566-1.738 11.68-4.874l28.074-28.273c2.917-2.939 2.9-7.687-.039-10.606zm-47.013-110.481h-24.446c-9.098 0-16.5 7.402-16.5 16.5v54.097c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.978c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.978c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.097c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5zm230.011-127.145h-34.38c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5h34.38c12.341 0 22.38 10.04 22.38 22.38v421.84c0 12.34-10.04 22.38-22.38 22.38h-313.72c-12.341 0-22.38-10.04-22.38-22.38v-421.84c0-12.34 10.04-22.38 22.38-22.38h56.803v14.88h-51.823c-6.881 0-12.48 5.599-12.48 12.479v308.854c0 4.143 3.358 7.5 7.5 7.5s7.5-3.357 7.5-7.5v-306.333h35.637c-.821 2.33-1.273 4.832-1.273 7.439v20.802c0 12.374 10.066 22.44 22.439 22.44h185.113c12.373 0 22.439-10.066 22.439-22.44v-20.801c0-2.607-.452-5.11-1.273-7.439h35.637v328.83h-38.919c-20.61 0-37.378 16.77-37.378 37.383v38.927h-222.422v-68.807c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v71.327c0 6.881 5.599 12.479 12.48 12.479h232.44c1.994.003 3.9-.803 5.304-2.197l.018-.018 76.282-76.292c1.276-1.284 2.057-3.018 2.173-4.824.004-.067.024-339.16.024-339.33 0-6.881-5.599-12.479-12.48-12.479h-51.823v-20.807c-.001-13.494-10.978-24.473-24.471-24.473h-151.174c-10.288 0-19.107 6.386-22.719 15.4h-58.554c-20.612 0-37.38 16.769-37.38 37.38v421.84c0 20.611 16.769 37.38 37.38 37.38h313.72c20.612 0 37.38-16.769 37.38-37.38v-421.84c0-20.611-16.768-37.38-37.38-37.38zm-68.797 439.409v-28.315c0-12.342 10.039-22.383 22.378-22.383h28.313zm-173.12-430.336c0-5.224 4.248-9.473 9.47-9.473h151.174c5.222 0 9.47 4.249 9.47 9.473v27.521c0 5.224-4.248 9.473-9.47 9.473h-151.174c-5.222 0-9.47-4.249-9.47-9.473zm-14.939 43.247c0-2.091.869-3.98 2.262-5.333 3.915 8.311 12.368 14.079 22.147 14.079h151.174c9.778 0 18.232-5.768 22.147-14.079 1.393 1.353 2.262 3.242 2.262 5.333v20.802c0 4.103-3.337 7.44-7.439 7.44h-185.114c-4.102 0-7.439-3.338-7.439-7.44zm-14.101 247.518c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.978c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.978c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.097c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-24.446c-9.098 0-16.5 7.402-16.5 16.5zm113.722 46.5h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm-113.722 55.596c0 9.098 7.402 16.5 16.5 16.5h54.097c9.098 0 16.5-7.402 16.5-16.5v-18.977c0-4.143-3.358-7.5-7.5-7.5s-7.5 3.357-7.5 7.5v18.977c0 .827-.673 1.5-1.5 1.5h-54.097c-.827 0-1.5-.673-1.5-1.5v-54.096c0-.827.673-1.5 1.5-1.5h24.446c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-24.446c-9.098 0-16.5 7.402-16.5 16.5zm113.722-25.596h61.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-61.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-102.096h61.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-61.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-30h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-72.097h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5zm0-30h106.972c4.142 0 7.5-3.357 7.5-7.5s-3.358-7.5-7.5-7.5h-106.972c-4.142 0-7.5 3.357-7.5 7.5s3.358 7.5 7.5 7.5z"/></g></svg>
				<?php esc_html_e( $strings , 'wp-travel' ); ?>
			</button>
		</a>
	<?php endif; ?>
	<?php
	if ( 'yes' === $enable_enquiry ) :
		wptravel_get_enquiries_form();
	endif;
}

/**
 * Single trip code.
 *
 * @param mixed $wp_travel_itinerary Itinerary datas.
 */
function wptravel_single_itinerary_trip_code( $wp_travel_itinerary ) {
	?>
	<div class="trip-code">
		<?php
		$strings         = WpTravel_Helpers_Strings::get();
		$trip_code_label = $strings['trip_code'];
		echo esc_html( $trip_code_label );
		?>
		: <span><?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?></span>
	</div>
	<?php
}

/**
 * Single trip tabs and price section.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_trip_tabs_and_price( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	?>
	<div class="wti__nav-tabs">
		<div class="wti__container">
			<div class="wti__grid">
				<div class="wti__grid-item col-lg-8">
					<div id="scrollspy-buttons" class="wti__scrollspy-buttons">
					<!-- <span class="line"></span> -->
					<?php
					$wp_travel_itinerary_tabs = wptravel_get_frontend_tabs();
					$index                    = 1;
					if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) {
						foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) {
							?>
							<?php if ( 'reviews' === $tab_key && ! comments_open() ) : ?>
								<?php continue; ?>
							<?php endif; //phpcs:ignore ?>
							<?php if ( 'yes' !== $tab_info['show_in_menu'] ) : ?>
								<?php continue; ?>
							<?php endif; //phpcs:ignore ?>
							<?php
							$tab_label = $tab_info['label'];
							if ( 'booking' === $tab_key ) {
								$tab_key = 'wti__booking'; // To scroll down to booking tab on right side.
							}
							?>
							<div class="wti__itinerary_tabs">
								<button class="scroll-spy-button <?php echo esc_attr( $tab_key ); ?> <?php echo esc_attr( $tab_info['label_class'] ); ?>" data-scroll='#<?php echo esc_attr( $tab_key ); ?>'>
									<?php echo esc_attr( $tab_label ); ?>
								</button>
							</div>
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
							$strings                          = WpTravel_Helpers_Strings::get();
							$args                             = $args_regular = array( 'trip_id' => $trip_id ); //phpcs:ignore
							$args_regular['is_regular_price'] = true;
							$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
							$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
							$enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
								array(
									'trip_id' => $trip_id,
									'from_price_sale_enable' => true,
								)
							);
							?>
							<span class="wti-price-from"><?php echo esc_html( $strings['from'] ); ?>: </span>
							<strong class="price-figure">
								<?php if ( $enable_sale ) : ?>
								<del>
									<span><?php echo wptravel_get_formated_price_currency( $regular_price, true ); //phpcs:ignore ?></span>
								</del>
								<?php endif; ?>
								<span class="wti_trip_price">
									<ins>
										<span><?php echo wptravel_get_formated_price_currency( $trip_price ); //phpcs:ignore ?></span>
									</ins>
								</span>
							</strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Single trip frontend contents.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_trip_contents( $trip_id ) {
	?>
	<div class="wti__content-wrapper">
		<div class="wti__container">
			<div class="wti__grid">
				<div class="wti__grid-item col-lg-8">
					<div class="wti__tab-content-area">
						<?php
						/**
						 * Hook 'wp_travel_single_trip_facts'.
						 *
						 * @hooked 'wp_travel_single_itinerary_trip_facts'.
						 */
						do_action( 'wp_travel_single_trip_facts' );

						/**
						 * Hook 'wp_travel_before_single_trip_main_contents'.
						 *
						 * @hooked 'wp_travel_single_itinerary_maps'.
						 */
						do_action( 'wp_travel_before_single_trip_main_contents', $trip_id );

						/**
						 * Hook 'wp_travel_single_trip_main_contents'.
						 *
						 * @hooked 'wp_travel_single_itinerary_main_contents'.
						 */
						do_action( 'wp_travel_single_trip_main_contents', $trip_id );
						?>
					</div>
				</div>
				<div class="wti__grid-item col-lg-4">
					<!-- #Trip meta info start -->
					<?php
					/**
					 * Hook 'wp_travel_single_trip_meta_information'.
					 *
					 * @hooked 'wp_travel_single_itinerary_meta_info'.
					 */
					do_action( 'wp_travel_single_trip_meta_information', $trip_id );
					?>
					<!-- #Trip meta info end -->
					<!-- #Booking Area Start -->
					<div class="wti__booking-area">
						<div id="wti__booking" class="wti__booking">
						<!-- Handle with React from here-->
						</div>
					</div>
					<!-- #Booking Area End -->
				</div>
				<!-- #Related Trips Start -->
				<?php
				/**
				 * Hook 'wp_travel_single_itinerary_related_trips'.
				 *
				 * @hooked 'wp_travel_single_related_trips'.
				 */
				do_action( 'wp_travel_single_itinerary_related_trips', $trip_id );
				?>
				<!-- #Related Trips End -->
			</div>
		</div>
	</div>
	<?php
}

/**
 * Single itinerary trip facts.
 */
function wptravel_single_itinerary_trip_facts() {

	$settings = wptravel_get_settings();

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

	/**
	 * To fix fact not showing on frontend since v4.0 or greater.
	 *
	 * Modified @since v4.4.1
	 */
	$settings_facts = $settings['wp_travel_trip_facts_settings'];
	if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {
		?>
		<div class="wti__trip-info">
			<?php
			foreach ( $wp_travel_trip_facts as $key => $trip_fact ) :
				?>
				<?php
				$trip_fact_id = $trip_fact['fact_id'];
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
						<?php
						if ( $trip_fact['type'] === 'multiple' ) {
							$count = count( $trip_fact['value'] );
							$i     = 1;
							foreach ( $trip_fact['value'] as $key => $val ) {
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
		</div>
	<?php } ?>
	<?php
}

/**
 * Single trip main contents.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_itinerary_main_contents( $trip_id ) {

	$wp_travel_itinerary_tabs = wptravel_get_frontend_tabs();

	if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) :
		$index = 1;
		foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) :
			$tab_label = $tab_info['label'];
			if ( 'reviews' === $tab_key && ! comments_open() ) :
				continue;
			endif;
			if ( 'yes' !== $tab_info['show_in_menu'] ) :
				continue;
			endif;

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
						$faqs = wptravel_get_faqs( get_the_id() );
						if ( is_array( $faqs ) && count( $faqs ) > 0 ) {
							?>
							<?php foreach ( $faqs as $k => $faq ) : ?>
							<!-- New -->
								<div class="accordion-panel">
									<div class="accordion-panel-heading">
										<div class="accordion-panel-title">
											<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" class="svg-inline--fa fa-caret-down fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z"></path></svg>
											<h3 class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $k + 1 ); ?>" aria-expanded="true">
											<?php echo esc_html( $faq['question'] ); ?>
											<span class="collapse-icon"></span>
											</h3>
										</div>
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
					echo wp_kses_post( $tab_info['content'] );

					$itinerary_list_template = wptravel_get_template( 'itineraries-list-new.php' );
					load_template( $itinerary_list_template );
					?>
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
					<?php
					break;
			}
			$index++;
		endforeach;
	endif;
}

/**
 * Single related trips.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_related_trips( $trip_id ) {
	$settings               = wptravel_get_settings();
	$hide_related_itinerary = ( isset( $settings['hide_related_itinerary'] ) && '' !== $settings['hide_related_itinerary'] ) ? $settings['hide_related_itinerary'] : 'no';

	if ( 'yes' === $hide_related_itinerary ) {
		return;
	}

	// For use in the loop, list 5 post titles related to first tag on current post.
	$terms = wp_get_object_terms( $trip_id, 'itinerary_types' );

	$wrapper_class = wptravel_get_theme_wrapper_class();
	?>
	<div class="wti__grid-item col-12">
		<div class="wti__related-trips <?php echo esc_attr( $wrapper_class ); ?>">
			<hr class="wti__trip-section-devider">
			<h3 class="related-trip-title"><?php echo apply_filters( 'wp_travel_related_post_title', esc_html__( 'Related Trips', 'wp-travel' ) ); ?></h3>
			<div class="wti__list-wrapper">
				<div class="wti__list">
				<?php
				if ( ! empty( $terms ) ) {
					$term_ids    = wp_list_pluck( $terms, 'term_id' );
					$col_per_row = apply_filters( 'wp_travel_related_itineraries_col_per_row', '3' );
					$args        = array(
						'post_type'      => WP_TRAVEL_POST_TYPE,
						'post__not_in'   => array( $trip_id ),
						'posts_per_page' => $col_per_row,
						'tax_query'      => array(
							array(
								'taxonomy' => 'itinerary_types',
								'field'    => 'id',
								'terms'    => $term_ids,
							),
						),
					);
					$query       = new WP_Query( $args );
					if ( $query->have_posts() ) {
						?>
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<?php wptravel_get_template_part( 'shortcode/itinerary', 'item-new' ); ?>
						<?php endwhile; ?>
						<?php
					} else {
						wptravel_get_template_part( 'shortcode/itinerary', 'item-none' );
					}
					wp_reset_postdata();
				} else {
					wptravel_get_template_part( 'shortcode/itinerary', 'item-none' );
				}
				?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Single trip meta information.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_itinerary_meta_info( $trip_id ) {
	$strings               = WpTravel_Helpers_Strings::get();
	$trip_type_text        = isset( $strings['trip_type'] ) ? $strings['trip_type'] : __( 'Trip Type', 'wp-travel' );
	$empty_trip_type_text  = isset( $strings['empty_results']['trip_type'] ) ? $strings['empty_results']['trip_type'] : __( 'No Trip Type', 'wp-travel' );
	$activities_text       = isset( $strings['activities'] ) ? $strings['activities'] : __( 'Activities', 'wp-travel' );
	$empty_activities_text = isset( $strings['empty_results']['activities'] ) ? $strings['empty_results']['activities'] : __( 'No Activities', 'wp-travel' );
	$group_size_text       = isset( $strings['group_size'] ) ? $strings['group_size'] : __( 'Group size', 'wp-travel' );
	$pax_text              = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel' );
	$empty_group_size_text = isset( $strings['empty_results']['group_size'] ) ? $strings['empty_results']['group_size'] : __( 'No Size Limit', 'wp-travel' );
	$wp_travel_itinerary   = new WP_Travel_Itinerary();
	?>
	<div class="wti__travel-info">
		<div class="wti__travel-info-wrapper">
			<div class="wti__travel-info-item">
				<div class="wti__travel-info_detail">
					<div class="wti__travel-info_name">
						<strong><?php echo esc_html( $trip_type_text ); ?></strong>
					</div>
					<div class="wti__travel-info_value">
					<?php
					$trip_types_list = $wp_travel_itinerary->get_trip_types_list();
					if ( $trip_types_list ) {
						echo wp_kses( $trip_types_list, wptravel_allowed_html( array( 'a' ) ) );
					} else {
						echo esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_trip_type_text ) ); // already filterable label using wp_travel_strings filter so this filter 'wp_travel_default_no_trip_type_text' need to remove in future.
					}
					?>
					</div>
				</div>
			</div><!-- wti_-travel-info-item -->
			<div class="wti__travel-info-item">
				<div class="wti__travel-info_detail">
					<div class="wti__travel-info_name">
						<strong><?php echo esc_html( $activities_text ); ?></strong>
					</div>
					<div class="wti__travel-info_value">
					<?php
					$activity_list = $wp_travel_itinerary->get_activities_list();
					if ( $activity_list ) {
						echo wp_kses( $activity_list, wptravel_allowed_html( array( 'a' ) ) );
					} else {
						echo esc_html( apply_filters( 'wp_travel_default_no_activity_text', $empty_activities_text ) ); // already filterable label using wp_travel_strings filter so this filter 'wp_travel_default_no_activity_text' need to remove in future.
					}
					?>
					</div>
				</div>
			</div><!-- wti_-travel-info-item -->
			<div class="wti__travel-info-item">
				<div class="wti__travel-info_detail">
					<div class="wti__travel-info_name">
						<strong><?php echo esc_html( $group_size_text ); ?></strong>
					</div>
					<div class="wti__travel-info_value">
					<?php
					$group_size = wptravel_get_group_size( $trip_id );
					if ( (int) $group_size && $group_size < 999 ) {
						printf( apply_filters( 'wp_travel_template_group_size_text', __( '%d %s', 'wp-travel' ) ), esc_html( $group_size ), esc_html( ( $pax_text ) ) ); // phpcs:ignore
					} else {
						echo esc_html( apply_filters( 'wp_travel_default_group_size_text', $empty_group_size_text ) ); // already filterable label using wp_travel_strings filter so this filter 'wp_travel_default_group_size_text' need to remove in future.
					}
					?>
					</div>
				</div>
			</div><!-- wti_-travel-info-item -->
		</div>
	</div>
	<?php
}

/**
 * Single trip page map.
 *
 * @param int $trip_id Trip ID.
 */
function wptravel_single_itinerary_maps( $trip_id ) {
	global $wp_travel_itinerary;
	if ( ! $wp_travel_itinerary->get_location() ) {
		return;
	}
	$get_maps        = wptravel_get_maps();
	$current_map     = $get_maps['selected'];
	$show_google_map = ( 'google-map' === $current_map ) ? true : false;
	$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map );

	if ( ! $show_google_map ) {
		return;
	}
	$settings = wptravel_get_settings();
	$api_key  = '';
	if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
		$api_key = $settings['google_map_api_key'];
	}

	$map_data = wptravel_get_map_data();
	$lat      = isset( $map_data['lat'] ) ? $map_data['lat'] : '';
	$lng      = isset( $map_data['lng'] ) ? $map_data['lng'] : '';

	$wrapper_class = wptravel_get_theme_wrapper_class();
	if ( '' !== $api_key && $show_google_map && ! empty( $lat ) && ! empty( $lng ) ) {
		?>
		<div id="trip-map" class="wti__tab-content-wrapper" class="wp-travel-map <?php echo esc_attr( $wrapper_class ); ?>">
		<h3 class="tab-content-title"><?php esc_html_e( 'Map', 'wp-travel' ); ?></h3>
			<div class="map">
				<div id="wp-travel-map" style="width:100%;height:300px"></div>
			</div>
		</div>
		<?php
	} else {
		$use_lat_lng = get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true );
		if ( 'yes' === $use_lat_lng ) {
			$q = "{$lat},{$lng}";
		} else {
			$q = $map_data['loc'];
		}
		if ( ! empty( $q ) ) :
			?>
			<div id="trip-map" class="wti__tab-content-wrapper" class="wp-travel-map  <?php echo esc_attr( $wrapper_class ); ?>">
			<h3 class="tab-content-title"><?php esc_html_e( 'Map', 'wp-travel' ); ?></h3>
				<div class="map">
					<iframe src="https://maps.google.com/maps?q=<?php echo $q; ?>&t=m&z=<?php echo $settings['google_map_zoom_level']; ?>&output=embed&iwloc=near" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
				</div>
			</div>
			<?php
		endif;
	}
}

// Archive itinerary sections.
/**
 * Archive filter.
 */
function wptravel_archive_before_content() {
	$view_mode = wptravel_get_archive_view_mode();
	if ( ( WP_Travel::is_page( 'archive' ) || is_search() ) && ! is_admin() ) {
		?>
		<div class="wti__container">
			<div class="wti__inner">
				<?php
				if ( WP_Travel::is_page( 'archive' ) ) {
					$strings        = WpTravel_Helpers_Strings::get();
					$price_text     = $strings['price'];
					$trip_type_text = $strings['trip_type'];
					$location_text  = $strings['location'];
					$show_text      = $strings['show'];
					$trip_date_text = $strings['trip_date'];
					$trip_name_text = $strings['trip_name'];
					?>
					<div class="wti__filter-bar">
						<div class="wti__filter">
							<div class="wti__filter-fields">
								<?php do_action( 'wp_travel_before_post_filter' ); ?>
								<input type="hidden" id="wp-travel-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>" />
								<?php
								$price     = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
								$type      = ! empty( $_GET['itinerary_types'] ) ? $_GET['itinerary_types'] : '';
								$location  = ! empty( $_GET['travel_locations'] ) ? $_GET['travel_locations'] : '';
								$trip_date = ! empty( $_GET['trip_date'] ) ? $_GET['trip_date'] : '';
								$trip_name = ! empty( $_GET['trip_name'] ) ? $_GET['trip_name'] : '';
								?>
								<div class="wti__filter-input wt__filter_by_trip_name">
									<select name="trip_name" class="wti__select wp_travel_input_filters trip-name">
										<option value=""><?php echo esc_html( $trip_name_text ); ?></option>
										<option value="asc" <?php selected( $trip_name, 'asc' ); ?> data-type="meta" ><?php esc_html_e( 'Ascending', 'wp-travel' ); ?></option>
										<option value="desc" <?php selected( $trip_name, 'desc' ); ?> data-type="meta" ><?php esc_html_e( 'Descending', 'wp-travel' ); ?></option>
									</select>
								</div>
								<div class="wti__filter-input wti__filter_by_price">
									<select name="price" class="wti__select wp_travel_input_filters price">
										<option value=""><?php echo esc_html( $price_text ); ?></option>
										<option value="low_high" <?php selected( $price, 'low_high' ); ?> data-type="meta" ><?php esc_html_e( 'Low to High', 'wp-travel' ); ?></option>
										<option value="high_low" <?php selected( $price, 'high_low' ); ?> data-type="meta" ><?php esc_html_e( 'High to Low', 'wp-travel' ); ?></option>
									</select>
								</div>
								<div class="wti__filter-input wti__filter_by_trip_type">
									<?php
									wp_dropdown_categories(
										array(
											'taxonomy'    => 'itinerary_types',
											'name'        => 'itinerary_types',
											'class'       => 'wti__select wp_travel_input_filters type',
											'show_option_none' => esc_html( $trip_type_text ),
											'option_none_value' => '',
											'selected'    => $type,
											'value_field' => 'slug',
										)
									);
									?>
								</div>
								<div class="wti__filter-input wti__filter_by_locations">
									<?php
									wp_dropdown_categories(
										array(
											'taxonomy'    => 'travel_locations',
											'name'        => 'travel_locations',
											'class'       => 'wti__select wp_travel_input_filters location',
											'show_option_none' => esc_html( $location_text ),
											'option_none_value' => '',
											'selected'    => $location,
											'value_field' => 'slug',
										)
									);
									?>
								</div>
								<div class="wti__filter-input wti__filter_by_trip_date">
									<select name="trip_date" class="wti__select wp_travel_input_filters trip-date">
										<option value=""><?php echo esc_html( $trip_date_text ); ?></option>
										<option value="asc" <?php selected( $trip_date, 'asc' ); ?> data-type="meta" ><?php esc_html_e( 'Ascending', 'wp-travel' ); ?></option>
										<option value="desc" <?php selected( $trip_date, 'desc' ); ?> data-type="meta" ><?php esc_html_e( 'Descending', 'wp-travel' ); ?></option>
									</select>
								</div>
								<button class="wti__filter-button wti__button btn-wp-travel-filter">
									<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
									viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
									<path d="M225.474,0C101.151,0,0,101.151,0,225.474c0,124.33,101.151,225.474,225.474,225.474
									c124.33,0,225.474-101.144,225.474-225.474C450.948,101.151,349.804,0,225.474,0z M225.474,409.323
									c-101.373,0-183.848-82.475-183.848-183.848S124.101,41.626,225.474,41.626s183.848,82.475,183.848,183.848
									S326.847,409.323,225.474,409.323z"/>
									<path d="M505.902,476.472L386.574,357.144c-8.131-8.131-21.299-8.131-29.43,0c-8.131,8.124-8.131,21.306,0,29.43l119.328,119.328
									c4.065,4.065,9.387,6.098,14.715,6.098c5.321,0,10.649-2.033,14.715-6.098C514.033,497.778,514.033,484.596,505.902,476.472z"/>
									</svg>
								</button>
							</div>
						</div>
						<div class="wti__grid-list-filter">
							<button data-view="grid-view" class="wti__filter-grid wti__button">
								<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
								<path d="M176.792,0H59.208C26.561,0,0,26.561,0,59.208v117.584C0,209.439,26.561,236,59.208,236h117.584
								C209.439,236,236,209.439,236,176.792V59.208C236,26.561,209.439,0,176.792,0z M196,176.792c0,10.591-8.617,19.208-19.208,19.208
								H59.208C48.617,196,40,187.383,40,176.792V59.208C40,48.617,48.617,40,59.208,40h117.584C187.383,40,196,48.617,196,59.208
								V176.792z"/>
								<path d="M452,0H336c-33.084,0-60,26.916-60,60v116c0,33.084,26.916,60,60,60h116c33.084,0,60-26.916,60-60V60
								C512,26.916,485.084,0,452,0z M472,176c0,11.028-8.972,20-20,20H336c-11.028,0-20-8.972-20-20V60c0-11.028,8.972-20,20-20h116
								c11.028,0,20,8.972,20,20V176z"/>
								<path d="M176.792,276H59.208C26.561,276,0,302.561,0,335.208v117.584C0,485.439,26.561,512,59.208,512h117.584
								C209.439,512,236,485.439,236,452.792V335.208C236,302.561,209.439,276,176.792,276z M196,452.792
								c0,10.591-8.617,19.208-19.208,19.208H59.208C48.617,472,40,463.383,40,452.792V335.208C40,324.617,48.617,316,59.208,316h117.584
								c10.591,0,19.208,8.617,19.208,19.208V452.792z"/>
								<path d="M452,276H336c-33.084,0-60,26.916-60,60v116c0,33.084,26.916,60,60,60h116c33.084,0,60-26.916,60-60V336
								C512,302.916,485.084,276,452,276z M472,452c0,11.028-8.972,20-20,20H336c-11.028,0-20-8.972-20-20V336c0-11.028,8.972-20,20-20
								h116c11.028,0,20,8.972,20,20V452z"/>
								</svg>
							</button>
							<button data-view="list-view" class="wti__filter-list wti__button active">
								<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
								<path d="M448.18,80h-320c-17.673,0-32,14.327-32,32s14.327,32,32,32h320c17.673,0,32-14.327,32-32S465.853,80,448.18,80z"/>
								<path d="M64.18,112c-0.036-8.473-3.431-16.586-9.44-22.56c-12.481-12.407-32.639-12.407-45.12,0
								C3.61,95.414,0.215,103.527,0.18,112c-0.239,2.073-0.239,4.167,0,6.24c0.362,2.085,0.952,4.124,1.76,6.08
								c0.859,1.895,1.876,3.715,3.04,5.44c1.149,1.794,2.49,3.457,4,4.96c1.456,1.45,3.065,2.738,4.8,3.84
								c1.685,1.227,3.512,2.248,5.44,3.04c2.121,1.1,4.382,1.908,6.72,2.4c2.073,0.232,4.167,0.232,6.24,0
								c8.45,0.007,16.56-3.329,22.56-9.28c1.51-1.503,2.851-3.166,4-4.96c1.164-1.725,2.181-3.545,3.04-5.44
								c1.021-1.932,1.826-3.971,2.4-6.08C64.419,116.167,64.419,114.073,64.18,112z"/>
								<path d="M64.18,256c0.238-2.073,0.238-4.167,0-6.24c-0.553-2.065-1.359-4.053-2.4-5.92c-0.824-1.963-1.843-3.839-3.04-5.6
								c-1.109-1.774-2.455-3.389-4-4.8c-12.481-12.407-32.639-12.407-45.12,0C3.61,239.414,0.215,247.527,0.18,256
								c0.062,4.217,0.875,8.388,2.4,12.32c0.802,1.893,1.766,3.713,2.88,5.44c1.217,1.739,2.611,3.348,4.16,4.8
								c1.414,1.542,3.028,2.888,4.8,4c1.685,1.228,3.511,2.249,5.44,3.04c1.951,0.821,3.992,1.412,6.08,1.76
								c2.047,0.459,4.142,0.674,6.24,0.64c2.073,0.239,4.167,0.239,6.24,0c2.036-0.349,4.024-0.94,5.92-1.76
								c1.981-0.786,3.861-1.807,5.6-3.04c1.772-1.112,3.386-2.458,4.8-4c1.542-1.414,2.888-3.028,4-4.8c1.23-1.684,2.251-3.51,3.04-5.44
								c1.093-2.124,1.9-4.384,2.4-6.72C64.426,260.167,64.426,258.073,64.18,256z"/>
								<path d="M64.18,400c0.237-2.073,0.237-4.167,0-6.24c-0.553-2.116-1.359-4.157-2.4-6.08c-0.859-1.895-1.876-3.715-3.04-5.44
								c-1.112-1.772-2.458-3.386-4-4.8c-12.481-12.407-32.639-12.407-45.12,0c-1.542,1.414-2.888,3.028-4,4.8
								c-1.164,1.725-2.181,3.545-3.04,5.44c-0.83,1.948-1.421,3.99-1.76,6.08c-0.451,2.049-0.665,4.142-0.64,6.24
								c0.036,8.473,3.431,16.586,9.44,22.56c1.414,1.542,3.028,2.888,4.8,4c1.685,1.228,3.511,2.249,5.44,3.04
								c1.951,0.821,3.992,1.412,6.08,1.76c2.047,0.459,4.142,0.674,6.24,0.64c2.073,0.239,4.167,0.239,6.24,0
								c2.036-0.349,4.024-0.94,5.92-1.76c1.981-0.786,3.861-1.807,5.6-3.04c1.772-1.112,3.386-2.458,4.8-4
								c1.542-1.414,2.888-3.028,4-4.8c1.231-1.683,2.252-3.51,3.04-5.44c1.092-2.125,1.899-4.384,2.4-6.72
								C64.426,404.167,64.426,402.073,64.18,400z"/>
								<path d="M480.18,224h-352c-17.673,0-32,14.327-32,32s14.327,32,32,32h352c17.673,0,32-14.327,32-32S497.853,224,480.18,224z"/>
								<path d="M336.18,368h-208c-17.673,0-32,14.327-32,32c0,17.673,14.327,32,32,32h208c17.673,0,32-14.327,32-32
								C368.18,382.327,353.853,368,336.18,368z"/>
								</svg>
							</button>
						</div>
					</div>
					<?php
				}
				?>
				<?php

				$archive_sidebar_class = '';

				if ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
					$archive_sidebar_class = 'has-sidebar';
				}

				?>
				<!-- For turn on sidebar (add 'has-sidebar', 'sidebar-left' class into 'wti__list-wrapper') -->
				<div class="wti__list-wrapper list-view <?php echo esc_attr( $archive_sidebar_class ); ?>">
					<div class="wti__item-lists">
						<div class="wti__list">
		<?php
	}
}

/**
 * Wrapper close for archive v2.
 *
 * @since 4.4.6
 * @return void
 */
function wptravel_archive_v2_wrapper_close() {
	if ( ( WP_Travel::is_page( 'archive' ) || is_search() ) && ! is_admin() ) {
		?>
		<?php
		$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
		$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
		?>
						</div> <!-- #wti__list -->
						<?php wptravel_pagination( $pagination_range, $max_num_pages ); ?>
					</div> <!-- #wti__list-items -->
					<?php do_action( 'wp_travel_archive_v2_listing_sidebar' ); ?>
				</div> <!-- #wti__list-wrapper -->
			</div> <!-- #wti__inner -->
		</div> <!-- #wti__container -->
		<?php
	}
}

/**
 * Archive v2 page sidebar
 *
 * @since 4.4.6
 * @return void
 */
function wptravel_archive_v2_listing_sidebar() {

	if ( ( WP_Travel::is_page( 'archive' ) || WP_Travel::is_page( 'search' ) ) && ! is_admin() && is_active_sidebar( 'wp-travel-archive-sidebar' ) ) :
		?>

		<div class="wti__sidebar" role="complementary">
			<?php dynamic_sidebar( 'wp-travel-archive-sidebar' ); ?>
		</div>

		<?php

	endif;

}
