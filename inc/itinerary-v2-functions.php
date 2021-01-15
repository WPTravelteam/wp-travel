<?php
/**
 * Itinerary v2 Template Functions.
 *
 * @package wp-travel/inc/
 */

// Hooks

add_action( 'wp_travel_itinerary_v2_hero_section', 'wp_travel_hero_section' );
add_action( 'wp_travel_itinerary_v2_hero_banner_image', 'wp_travel_hero_section_banner_image' );

/**
 * Main hero section for itinerary page.
 *
 * @param int $trip_id.
 * @return void.
 */
function wp_travel_hero_section( $trip_id ) {

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
                    <?php
                    /**
                     * Hook 'wp_travel_before_single_title'.
                     *
                     * @param int trip_id.
                     */
                    do_action( 'wp_travel_before_single_title', get_the_ID() ); 
					wp_travel_do_deprecated_action( 'wp_tarvel_before_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_before_single_title' );
                    $show_title = apply_filters( 'wp_travel_show_single_page_title', true );

                    if ( $show_title ) {
                        the_title( '<h1 class="wti__trip-title">', '</h1>' );
                    }

                    /**
                     * Hook 'wp_travel_single_trip_after_title'.
                     *
                     * @param int trip_id.
                     */
                    do_action( 'wp_travel_single_trip_after_title', get_the_ID() );
                    wp_travel_do_deprecated_action( 'wp_travel_after_single_title', array( get_the_ID() ), '2.0.4', 'wp_travel_single_trip_after_title' );  // @since 1.0.4 and deprecated in 2.0.4
                    ?>
                    <div class="wti__trip-meta">
                        <div class="trip__location">
                            <?php
                                $i = 0;
                                $terms = get_the_terms( $trip_id, 'travel_locations' );										
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
                            $average_rating = wp_travel_get_average_rating( $trip_id ); 									
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
    <?php
}

/**
 * Main banner for itinerary page.
 *
 * @param int $trip_id.
 * @return void.
 */
function wp_travel_hero_section_banner_image( $trip_id ) {
    ?>
    <img src="<?php echo esc_url( wp_travel_get_post_thumbnail_url( $trip_id, 'large' ) ) ?>" alt="">
    <?php
}