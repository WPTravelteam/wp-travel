<?php
/**
 * Callback for Debug tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wptravel_settings_callback_debug( $tab, $args ) {
		$settings = $args['settings'];

		$wt_test_mode  = $settings['wt_test_mode'];
		$wt_test_email = $settings['wt_test_email'];
		/**
		 * @since 4.0.6
		 * Load Optimize option.
		 */
		$wt_load_optimized_script = isset( $settings['wt_load_optimized_script'] ) ? $settings['wt_load_optimized_script'] : 'no';
	?>
		<h3><?php esc_html_e( 'Test Payment', 'wp-travel' ); ?></h3>
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
		<?php
		/**
		 * @since 4.0.6
		 */
		?>
		<h3><?php esc_html_e( 'Optimized Scripts and Styles', 'wp-travel' ); ?></h3>
		<table class="form-table">
		<tr>
				<th><label for="wt_load_optimized_script"><?php esc_html_e( 'Load Optimized Scripts', 'wp-travel' ); ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="wt_load_optimized_script" type="hidden" />
							<input type="checkbox" value="yes" <?php checked( 'yes', $wt_load_optimized_script ); ?> name="wt_load_optimized_script" id="wt_load_optimized_script"/>
							<span class="switch">
						</span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( 'Enabling this will load the optimized bundled scripts files.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_below_debug_tab_fields', $args ); ?>
		<?php
}
