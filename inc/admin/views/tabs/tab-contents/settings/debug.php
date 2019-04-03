<?php
/**
 * Callback for Debug tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_debug( $tab, $args ) {
	$settings = $args['settings'];

	$wt_test_mode  = $settings['wt_test_mode'];
	$wt_test_email = $settings['wt_test_email'];
	?>
	<h4 class="wp-travel-tab-content-title"><?php esc_html_e( 'Test Payment', 'wp-travel' ); ?></h4>

	<div class="form_field">
		<label class="label_title" for="wt_test_mode"><?php esc_html_e( 'Test Mode', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="wt_test_mode" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $wt_test_mode ); ?> name="wt_test_mode" id="wt_test_mode" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="wt_test_mode">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
	</div>
	<div class="form_field">
		<label for="wt_test_email" class="control-label label_title"><?php esc_html_e( 'Test Email', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="text" value="<?php echo esc_attr( $wt_test_email ); ?>" name="wt_test_email" id="wt_test_email"/>
			<figcaption><?php esc_html_e( 'Test email address will get test mode payment emails.', 'wp-travel' ); ?></figcaption>
		</div>
	</div>
	<?php do_action( 'wp_travel_below_debug_tab_fields', $args ); ?>
	<?php
}
