<?php
/**
 * Front End Output Class for Tour Extras.
 * 
 * @package WP_Travel_Tour_Extras
 */
class Wp_Travel_Extras_Frontend {

    public function __construct() {
        //Init Class.
        $this->init();

    }
    /**
     * Init Hooks
     * @return void
     */
    public function init() {

        add_action( 'wp_travel_trip_extras', array( $this, 'tour_extras_frontend' ) );
        add_action( 'wp_travel_tour_extras_cart_block', array( $this, 'wp_travel_tour_extras_cart_block' ) );
        
    }
    /**
     * Is Extras Pro
     *
     * @return boolean
     */
    public function is_extras_pro() {

        return class_exists( 'WP_Travel_Tour_Extras' );

    }
    /**
     * has_trip_extras Check if the privided trips has extras added.
     * 
     * @param int trip id.
     * @return bool true | false
     */
    public function has_trip_extras( $trip_id, $price_key = false ) {

        if ( empty( $trip_id ) )
            return false;

        if ( $price_key ) {
            $pricing_options = wp_travel_get_pricing_variation( $trip_id, $price_key );
            $pricing_option  = ( is_array( $pricing_options ) && ! empty( $pricing_options ) ) ? reset( $pricing_options ) : false;

            if ( $pricing_option ) {
                $trip_extras = isset( $pricing_option['tour_extras'] ) ? $pricing_option['tour_extras'] : array();
            }
        } else {

            $trip_extras = get_post_meta( $trip_id, 'wp_travel_tour_extras', true );
        }

        return ( is_array( $trip_extras ) && ( count( $trip_extras ) > 0 ) ) ? true : false; 

    }
    /**
     * get_trip_extras
     * 
     * @param int trip id.
     * @return array Trip Extras array for the trip.
     */
    public function get_trip_extras( $trip_id, $price_key = false ) {
        if ( empty( $trip_id ) )
            return;

        $trip_extras = array();

        if ( $this->has_trip_extras( $trip_id, $price_key ) ) {
            if ( $price_key ) {
                $pricing_options = wp_travel_get_pricing_variation( $trip_id, $price_key );
                $pricing_option  = ( is_array( $pricing_options ) && ! empty( $pricing_options ) ) ? reset( $pricing_options ) : false;
    
                if ( $pricing_option )
                    $trip_extras = isset( $pricing_option['tour_extras'] ) ? $pricing_option['tour_extras'] : array();
            
            } else {
    
                $trip_extras = get_post_meta( $trip_id, 'wp_travel_tour_extras', true );
            }
        }
        
        return $trip_extras;

    }
    /**
     * Tour Extras Frontend layout
     *
     * @param int $trip_id
     * @return void
     */
    public function tour_extras_frontend( $price_key = false ) {

        global $post;

        if ( ! $post )
            return;

        $trip_id = $post->ID;

        $trip_extras = $this->get_trip_extras( $trip_id );

        if ( $price_key )
            $trip_extras = $this->get_trip_extras( $trip_id, $price_key );

        if ( is_array( $trip_extras ) && ! empty ( $trip_extras ) ) :

            print_r( $trip_extras );

            if ( $this->is_extras_pro() ) {
                do_action( 'wp_travel_extras_pro_extras_layout', $trip_extras );
            } else {
                ?>
                <div class="wp_travel_tour_extras">
                    <h3><?php 
                        $trip_extras_heading = apply_filters( 'wp_travel_trip_extras_heading', __( 'Trip Includes:', 'wp-travel' ) );    
                        echo esc_html( $trip_extras_heading ); 
                    ?></h3>
                    <div class="wp_travel_tour_extras_content">
                        <?php foreach( $trip_extras as $key => $extra ) : ?>
                            <div class="wp_travel_tour_extras_option_single">
                            <div class="wp_travel_tour_extras_option_single_content">
                                <div class="wp_travel_tour_extras_option_top">
                                    <input checked id="trip_extra_<?php echo esc_attr( $key ) ?>" type="checkbox">
                                    <label for="trip_extra_<?php echo esc_attr( $key ) ?>" class="check_icon"></label>
                                    <div class="wp_travel_tour_extras_option_label">
                                        <div class="wp_travel_tour_extras_title">
                                            <h5><?php echo esc_html( get_the_title( $extra ) ); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php
            }
        
        endif;

    }
    /**
     * Tour extras Cart Block layout
     *
     * @param int $trip_id
     * @return void
     */
    public function wp_travel_tour_extras_cart_block( $trip_id = NULL ) {

        /**
         * Tour Extras Front End extras HTML
         */
        ?>
        <div class="wp_travel_tour_extras">
            <h3>Extras:</h3>
            <div class="wp_travel_tour_extras_content">
                <div class="wp_travel_tour_extras_option_single"><!-- Loop This -->
                    <div class="wp_travel_tour_extras_option_single_content">
                        <div class="wp_travel_tour_extras_option_top">
                            <!-- <input id="test_id1" type="checkbox">
                            <label for="test_id1" class="check_icon"></label> -->
                            <a href="#" class="check_icon"></a>
                            <div class="wp_travel_tour_extras_option_label">
                                <div class="wp_travel_tour_extras_title">
                                    <h5>Additional Night</h5>
                                </div>
                                <div class="wp_travel_tour_extras_price">
                                    <span>Price:</span> 
                                    <ins>$5</ins>
                                </div>
                                <div class="wp_travel_tour_extras_quantity">
                                    <span>Qty:</span> 
                                    <input type="number">
                                </div>
                                <div class="wp_travel_tour_extras_total_price">
                                    <span>Total:</span> 
                                    <strong>$5</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    }
    

}

// Run the Class. | Construct.
new Wp_Travel_Extras_Frontend();
