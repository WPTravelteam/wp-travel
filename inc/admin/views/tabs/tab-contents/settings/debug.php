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

	<table class="form-table">
		<tr>
			<th><label for="wt_test_mode"><?php esc_html_e( 'Test Mode', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="wt_test_mode" type="hidden" />
						<input type="checkbox" value="yes" <?php checked( 'yes', $wt_test_mode ); ?> name="wt_test_mode" id="wt_test_mode"/>
						<span class="switch">
					</span>
					</label>
				</span>
				<p class="description"><?php esc_html_e( 'Enable test mode to make test payment.', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="wt_test_email"><?php esc_html_e( 'Test Email', 'wp-travel' ); ?></label></th>
			<td><input type="text" value="<?php echo esc_attr( $wt_test_email ); ?>" name="wt_test_email" id="wt_test_email"/>
			<p class="description"><?php esc_html_e( 'Test email address will get test mode payment emails.', 'wp-travel' ); ?></p>
			</td>
		</tr>
	</table>
		<!-- new design -->
		<div class="form_field">
				<label class="test_mode_title">enable trip enquiry</label>
				<div class="subject_input">
					<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked="">
					<label class="onoffswitch-label" for="myonoffswitch">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
				</div>
		</div>
                      
				<div class="form_field">
						<label for="inputEmail3" class="control-label test_mode_title">text email</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="inputEmail3" placeholder="New Subjects">
							<figcaption>
								Test email address will get test mode payment
								emails.
							</figcaption>
							</div>
			</div>
		<!-- ends -->

	<?php do_action( 'wp_travel_below_debug_tab_fields', $args ); ?>
	<?php
}
