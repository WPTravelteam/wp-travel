<?php
/**
 * Callback for Payment tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_payment( $tab, $args ) {
    $settings = $args['settings'];

    $partial_payment          = $settings['partial_payment'];
    $minimum_partial_payout   = $settings['minimum_partial_payout'];
    $paypal_email             = $settings['paypal_email'];
    $payment_option_paypal    = $settings['payment_option_paypal'];
    $trip_tax_enable          = $settings['trip_tax_enable'];
    $trip_tax_percentage      = $settings['trip_tax_percentage'];
    $trip_tax_price_inclusive = $settings['trip_tax_price_inclusive'];
    ?>

    <table class="form-table">
        <tr>
            <th><label for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ); ?></label></th>
            <td>
            <span class="show-in-frontend checkbox-default-design">
                <label data-on="ON" data-off="OFF">
                    <input value="no" name="partial_payment" type="hidden" />
                    <input type="checkbox" value="yes" <?php checked( 'yes', $partial_payment ); ?> name="partial_payment" id="partial_payment"/>
                    <span class="switch">
                </span>

                </label>
            </span>
                <p class="description"><?php esc_html_e( 'Enable partial payment while booking.', 'wp-travel' ); ?>
                </p>
            </td>
        </tr>
        <tr id="wp-travel-minimum-partial-payout">
            <th><label for="minimum_partial_payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label></th>
            <td>
                <input type="range" min="1" max="100" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout" class="wt-slider" />
                <label><input type="number" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout_output" />%</label>
                <p class="description"><?php esc_html_e( 'Minimum percent of amount to pay while booking.', 'wp-travel' ); ?></p>
            </td>
        </tr>
    </table>
    <?php do_action( 'wp_travel_payment_gateway_fields', $args ); ?>
    <h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Standard Paypal', 'wp-travel' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="payment_option_paypal"><?php esc_html_e( 'Enable Paypal', 'wp-travel' ); ?></label></th>
            <td>
                <span class="show-in-frontend checkbox-default-design">
                <label data-on="ON" data-off="OFF">
                <input value="no" name="payment_option_paypal" type="hidden" />
                    <input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_paypal ); ?> name="payment_option_paypal" id="payment_option_paypal"/>
                    <span class="switch">
                </span>

                </label>
            </span>
                <p class="description"><?php esc_html_e( 'Check to enable standard PayPal payment.', 'wp-travel' ); ?></p>
            </td>
        </tr>
        <tr id="wp-travel-paypal-email" >
            <th><label for="paypal_email"><?php esc_html_e( 'Paypal Email', 'wp-travel' ); ?></label></th>
            <td>
                <input type="text" value="<?php echo esc_attr( $paypal_email ); ?>" name="paypal_email" id="paypal_email"/>
                <p class="description"><?php esc_html_e( 'PayPal email address that receive payment.', 'wp-travel' ); ?></p>
            </td>
        </tr>
    </table>
    <div class="wp-travel-upsell-message">
        <div class="wp-travel-pro-feature-notice">
            <h4><?php esc_html_e( 'Need more payment gateway options ?', 'wp-travel' ); ?></h4>
            <p><?php printf( __( '%1$sCheck All Payment Gateways %2$s OR %3$sRequest a new one%4$s', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/downloads">', '</a>', '<a target="_blank" href="http://wptravel.io/contact">', '</a>' ); ?></p>
        </div>
    </div>
    <h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'TAX Options', 'wp-travel' ); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="trip_tax_enable"><?php esc_html_e( 'Enable Tax for Trip Price', 'wp-travel' ); ?></label></th>
            <td>
                <span class="show-in-frontend checkbox-default-design">
                <label data-on="ON" data-off="OFF">
                    <input value="no" name="trip_tax_enable" type="hidden" />
                    <input type="checkbox" value="yes" <?php checked( 'yes', $trip_tax_enable ); ?> name="trip_tax_enable" id="trip_tax_enable"/>
                    <span class="switch">
                </span>

                </label>
            </span>
                <p class="description"><?php esc_html_e( 'Check to enable Tax options for trips.', 'wp-travel' ); ?></p>
            </td>
        </tr>
        <tr id="wp-travel-tax-price-options" >
            <th><label><?php esc_html_e( 'Trip prices entered with tax', 'wp-travel' ); ?></label></th>
            <td>
                <label><input <?php checked( 'yes', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="yes" type="radio">
                <?php esc_html_e( 'Yes, I will enter trip prices inclusive of tax', 'wp-travel' ); ?></label>

                <label> <input <?php checked( 'no', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="no" type="radio">
                <?php esc_html_e( 'No, I will enter trip prices exclusive of tax', 'wp-travel' ); ?></label>

                <p class="description"><?php esc_html_e( 'This option will affect how you enter trip prices.', 'wp-travel' ); ?></p>

            </td>
        </tr>
        <tr id="wp-travel-tax-percentage" <?php echo 'yes' == $trip_tax_price_inclusive ? 'style="display:none;"' : 'style="display:table-row;"'; ?> >
            <th><label for="trip_tax_percentage_output"><?php esc_html_e( 'Tax Percentage', 'wp-travel' ); ?></label></th>
            <td>

                <label><input type="number" min="0" max="100" step="0.01" value="<?php echo esc_attr( $trip_tax_percentage ); ?>" name="trip_tax_percentage" id="trip_tax_percentage_output" />%</label>
                <p class="description"><?php esc_html_e( 'Trip Tax percentage added to trip price.', 'wp-travel' ); ?></p>

            </td>
        </tr>
    </table>
    <?php
}