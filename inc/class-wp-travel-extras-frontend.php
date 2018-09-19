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
     * has_trip_extras Check if the privided trips has extras added.
     * 
     * @param int trip id.
     * @return bool true | false
     */
    public function has_trip_extras( $trip_id ) {

        if ( empty( $trip_id ) )
            return false;

    }
    /**
     * get_trip_extras
     * 
     * @param int trip id.
     * @return array Trip Extras array for the trip.
     */
    public function get_trip_extras( $trip_id ) {
        if ( empty( $trip_id ) )
            return;

        $trip_extras = array();

        if ( $this->has_trip_extras( $trip_id ) ) {

            $trip_extras = '';

        }
        
        
        return $trip_extras;

    }
    /**
     * Tour Extras Frontend layout
     *
     * @param int $trip_id
     * @return void
     */
    public function tour_extras_frontend( $trip_id = NULL ) {

        global $post;

        if ( ! $post )
            return;

        $trip_id = $post->ID;

        /**
         * Tour Extras Front End extras HTML
         */
        ?>
        <div class="wp_travel_tour_extras">
            <h3>Add Extra Options:</h3>
            <div class="wp_travel_tour_extras_content">
                <div class="wp_travel_tour_extras_option_single"><!-- Loop This -->
                    <div class="wp_travel_tour_extras_option_single_content">
                        <div class="wp_travel_tour_extras_option_top">
                            <input id="test_id1" type="checkbox">
                            <label for="test_id1" class="check_icon"></label>
                            <div class="wp_travel_tour_extras_option_label">
                                <div class="wp_travel_tour_extras_title">
                                    <h5>Additional Night</h5>
                                </div>
                                <div class="wp_travel_tour_extras_price">
                                    <del>$10</del><ins>$5</ins>
                                </div>
                                <i class="fa fa-angle-down wp_travel_tour_extras_toggler"></i>
                            </div>
                        </div>
                        <div class="wp_travel_tour_extras_option_bottom">
                            <div class="d-flex">
                                <figure class="wp_travel_tour_extras_image"><a href="http://localhost/travel-log/wp-content/uploads/2018/01/snow-3090067_1920-365x215.jpg"><img src="http://localhost/travel-log/wp-content/uploads/2018/01/snow-3090067_1920-365x215.jpg" alt=""></a></figure>
                                <div class="wp_travel_tour_extras_option_bottom_right">
                                    <div class="wp_travel_tour_extras_description">
                                        <p>Sit et aperiam dolore consectetur culpa error ipsa ullam qui dolor Nam labore nulla consectetur dolorum ut facere reprehenderit illum</p>
                                    </div>
                                    <div class="wp_travel_tour_extras_quantity">
                                        <input type="number">
                                    </div>
                                    <a href="#" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

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
