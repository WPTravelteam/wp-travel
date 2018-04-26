<?php
$date_array = wp_travel_get_date_array();

$days = $date_array['days'];
$months = $date_array['months'];
$years = $date_array['years'];
?>

<form method="post" >
    <?php foreach( $trips as $cart_id => $trip ) : ?>
        <div class="wp-travel-trip-details">
            <div class="section-title text-left">
            <h3><?php echo esc_html( get_the_title( $trip['trip_id'] ) ) ?><small> / 8 days 7 nights</small></h3>
            </div>
            
            <div class="panel-group number-accordion">
                <div class="panel ws-theme-timeline-block">
                    <div class="panel-heading">            
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
                        <h4 class="panel-title"><?php esc_html_e( 'Traveller Details', 'wp-travel' ) ?></h4>
                    </div>
                    <div id="number-accordion2" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="payment-content">
                                <div class="payment-traveller">
                                    <div class="row gap-0">
                                        <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
                                            <h6 class="heading mt-0 mb-15">Lead Traveller</h6>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'First Name', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-9">
                                                <input name="traveller_info_fname[<?php echo esc_attr( $cart_id ) ?>][]" type="text" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Last Name', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-9">
                                                <input name="traveller_info_lname[<?php echo esc_attr( $cart_id ) ?>][]" type="text" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20 select2-input-hide">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Gender', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-4 col-md-3">
                                                <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Gender" tabindex="-1" aria-hidden="true" name="traveller_info_gender[<?php echo esc_attr( $cart_id ) ?>][]" >
                                                    <option value=""><?php esc_html_e( 'Gender', 'wp-travel' ) ?></option>
                                                    <option value="male"><?php esc_html_e( 'Male', 'wp-travel' ) ?>.</option>
                                                    <option value="female"><?php esc_html_e( 'Female', 'wp-travel' ) ?>.</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20 select2-input-hide">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'DOB', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-6">
                                                <div class="row gap-15">
                                                    <div class="col-xs-4 col-sm-4">
                                                        <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Date" tabindex="-1" aria-hidden="true" name="traveller_info_dob_day[<?php echo esc_attr( $cart_id ) ?>][]">
                                                            <option value=""><?php esc_html_e( 'Day', 'wp-travel' ) ?></option>
                                                            <?php foreach( $days as $key => $day ) : ?>
                                                                <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $day ) ?></option>
                                                            <?php endforeach; ?>                                                       
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-4 col-sm-4">
                                                        <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Month" tabindex="-1" aria-hidden="true" name="traveller_info_dob_month[<?php echo esc_attr( $cart_id ) ?>][]">
                                                            <option value=""><?php esc_html_e( 'Month', 'wp-travel' ) ?></option>
                                                            <?php foreach( $months as $key => $month ) : ?>
                                                                <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $month ) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-xs-4 col-sm-4">
                                                        <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Year" tabindex="-1" aria-hidden="true" name="traveller_info_dpb_year[<?php echo esc_attr( $cart_id ) ?>][]" >
                                                            <option value=""><?php esc_html_e( 'Year', 'wp-travel' ) ?></option>
                                                            <?php foreach( $years as $key => $year ) : ?>
                                                                <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $year ) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Email', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-9">
                                                <input name="traveller_info_email[<?php echo esc_attr( $cart_id ) ?>][]" type="email" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Phone Number', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-9">
                                                <input name="traveller_info_phone[<?php echo esc_attr( $cart_id ) ?>][]" type="text" pattern="^[\d\+\-\.\(\)\/\s]*$" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-horizontal">
                                        <div class="form-group gap-20">
                                            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Country', 'wp-travel' ) ?>:</label>
                                            <div class="col-sm-8 col-md-9">
                                                <select class="select2-single form-control select2-hidden-accessible" data-placeholder="Nationality" tabindex="-1" aria-hidden="true" name="traveller_info_country[<?php echo esc_attr( $cart_id ) ?>][]" >
                                                    <option value=""><?php esc_html_e( 'Country', 'wp-travel' ) ?></option>
                                                    <?php foreach ( wp_travel_get_countries() as $key => $country ) : ?>
                                                        <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $country ) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>														
                            </div>
                            <div class="text-center">
                                <button class="btn btn-block simple center-div wp-travel-add-traveller" data-cart-id="<?php echo esc_attr( $cart_id ) ?>"><?php esc_html_e( 'Add another traveller', 'wp-travel' ) ?></button>
                            </div>
                        </div>
                    </div>
                </div>                                        
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Billing info -->
    <div class="panel ws-theme-timeline-block">
        <div class="panel-heading">
        
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
    <!-- Payment info -->
    <div class="panel ws-theme-timeline-block">
        <div class="panel-heading">
        
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
                    <input type="submit" name="traveller_info_book_now" id="wp-travel-book-now" value="Book and Pay"> 
                </div>




            </div>        
        </div>
        </div>
    </div>
</form>

<?php

function wp_travel_get_date_array() {
    $days = array();
    for( $i = 1; $i<=31; $i++ ) {
        $days[] = $i;
    }

    $months = array(
        'jan' => __( 'Jan', 'wp-travel' ),
        'feb' => __( 'Feb', 'wp-travel' ),
        'mar' => __( 'Mar', 'wp-travel' ),
        'apr' => __( 'Apr', 'wp-travel' ),
        'may' => __( 'May', 'wp-travel' ),
        'jun' => __( 'Jun', 'wp-travel' ),
        'jul' => __( 'Jul', 'wp-travel' ),
        'aug' => __( 'Aug', 'wp-travel' ),
        'sep' => __( 'Sep', 'wp-travel' ),
        'oct' => __( 'Oct', 'wp-travel' ),
        'nov' => __( 'Nov', 'wp-travel' ),
        'dec' => __( 'Dec', 'wp-travel' ),
    );

    $year_from = date( 'Y' );

    $year_upto = $year_from - 100;

    $years = array();
    for ( $i = $year_from; $i >= $year_upto; $i-- ) {
        $years[$i] = $i;
    }

    return array( 'years' => $years, 'months' => $months, 'days' => $days );
}

?>

<script type="text/html" id="tmpl-traveller-info">
<div class="payment-traveller added">
    <a href="#" class="pull-right font12 traveller-remove"><i class="fa fa-times-circle"></i></a>
    <div class="row gap-0">
        <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
            <h6 class="heading mt-0 mb-15"><?php esc_html_e( 'Traveller ' ) ?><span class="traveller-index">{{data.index}}</span></h6>
        </div>
    </div>
    <div class="form-horizontal">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'First Name', 'wp-travel' ) ?>:</label>
            <div class="col-sm-8 col-md-9">
                <input name="traveller_info_fname[{{{data.cart_id}}}][]" type="text" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="form-horizontal">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Last Name', 'wp-travel' ) ?>:</label>
            <div class="col-sm-8 col-md-9">
                <input name="traveller_info_lname[{{{data.cart_id}}}][]" type="text" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="form-horizontal">
        <div class="form-group gap-20 select2-input-hide">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Gender', 'wp-travel' ) ?>:</label>
            <div class="col-sm-4 col-md-3">
                <select class="select2-no-search form-control select2-hidden-accessible" data-placeholder="Gender" tabindex="-1" aria-hidden="true" name="traveller_info_gender[{{{data.cart_id}}}][]" >
                    <option value=""><?php esc_html_e( 'Gender', 'wp-travel' ) ?></option>
                    <option value="male"><?php esc_html_e( 'Male', 'wp-travel' ) ?>.</option>
                    <option value="female"><?php esc_html_e( 'Female', 'wp-travel' ) ?>.</option>
                </select>
            </div>
        </div>
    </div>   
    <div class="form-horizontal">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Country', 'wp-travel' ) ?>:</label>
            <div class="col-sm-8 col-md-9">
                <select class="select2-single form-control select2-hidden-accessible" data-placeholder="Nationality" tabindex="-1" aria-hidden="true" name="traveller_info_country[{{{data.cart_id}}}][]" >
                    <option value=""><?php esc_html_e( 'Country', 'wp-travel' ) ?></option>
                    <?php foreach ( wp_travel_get_countries() as $key => $country ) : ?>
                        <option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_attr( $country ) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>
</script>