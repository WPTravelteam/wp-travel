<?php
/**
 * Callback for Debug tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_debug( $tab, $args ) {
	$settings = $args['settings'];

		$wt_test_mode  = $settings['wt_test_mode'];
		$wt_test_email = $settings['wt_test_email'];
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
		if ( 'yes' == $wt_test_mode ) {
			?>
				<a href="edit.php?post_type=itinerary-booking&page=settings&clear_v4_migration=yes">clear v4 migration and table</a>
			<?php


			if ( isset( $_GET['clear_v4_migration'] ) && $_GET['clear_v4_migration'] == 'yes' ) {
				delete_option( 'wp_travel_migrate_400' );
				delete_option( 'wp_travel_pricing_table_created' );

				global $wpdb;
				
				if ( is_multisite() ) {
					$blog_id                       = get_current_blog_id();
					$pricings_table                = $wpdb->base_prefix . $blog_id . '_wt_pricings';
					$date_table                    = $wpdb->base_prefix . $blog_id . '_wt_dates';
					$price_category_relation_table = $wpdb->base_prefix . $blog_id . '_wt_price_category_relation';
					$excluded_dates_times_table    = $wpdb->base_prefix . $blog_id . '_wt_excluded_dates_times';
					
				} else {
					$pricings_table                = $wpdb->base_prefix . 'wt_pricings';
					$date_table                    = $wpdb->base_prefix . 'wt_dates';
					$price_category_relation_table = $wpdb->base_prefix . 'wt_price_category_relation';
					$excluded_dates_times_table    = $wpdb->base_prefix . 'wt_excluded_dates_times';
				}
				
				$wpdb->query("DROP TABLE IF EXISTS {$pricings_table}");
				$wpdb->query("DROP TABLE IF EXISTS {$date_table}");
				$wpdb->query("DROP TABLE IF EXISTS {$price_category_relation_table}");
				$wpdb->query("DROP TABLE IF EXISTS {$excluded_dates_times_table}");

			}
			
		}
		?>
		<?php do_action( 'wp_travel_below_debug_tab_fields', $args ); ?>
		<?php
}
