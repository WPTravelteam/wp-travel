<?php
/**
 * This file has the requrired codes for the class WpTravel_Addons_Settings.
 *
 * @package inc
 * @since 3.0.1
 */

if ( ! class_exists( 'WpTravel_Addons_Settings' ) ) {

	/**
	 * Class to generate toggle settings for the addons.
	 *
	 * Pass the addon name as the parameter.
	 * For ex: for the plugin wp travel multiple currency, add name as WP Travel Multiple Currency
	 * ==Code snipet==
	 * $addon_settings = new WpTravel_Addons_Settings( 'WP Travel Multiple Currency' );
	 * if ( ! ( $addon_settings->is_addon_active() ) ) {
	 * return;
	 * }
	 */
	class WpTravel_Addons_Settings {

		/**
		 * Plugin name.
		 *
		 * @var string
		 */
		public $plugin_name;

		/**
		 * Init class WP_Travel_Addons_Settings
		 *
		 * @param [type] $plugin Addon data.
		 */
		public function __construct( $plugin ) {
			$this->plugin_name = $plugin;

			$plugin_name = $this->plugin_name;
			$plugin_name = strtolower( $plugin_name );
			$plugin_name = str_replace( ' ', '_', $plugin_name );

			add_filter( 'wp_travel_settings_fields', array( $this, 'settings_fields' ) );
			add_action( 'wp_travel_addons_setings_tab_fields', array( $this, 'plugin_action' ) );

		}

		/**
		 * Checks if current addon is active or not.
		 *
		 * @return boolean
		 */
		public function is_addon_active() {
			$plugin_name  = $this->plugin_name;
			$plugin_name  = strtolower( $plugin_name );
			$plugin_name  = str_replace( ' ', '_', $plugin_name );
			$settings     = wptravel_get_settings();
			$enable_addon = isset( $settings[ 'show_' . $plugin_name ] ) ? $settings[ 'show_' . $plugin_name ] : 'yes';

			if ( 'yes' !== $enable_addon ) {
				return false;
			}
			return true;
		}

		/**
		 * Default settings fields.
		 *
		 * @param array $settings WP Travel Settings.
		 * @return array
		 */
		public function settings_fields( $settings ) {
			$plugin_name = $this->plugin_name;
			$plugin_name = strtolower( $plugin_name );
			$plugin_name = str_replace( ' ', '_', $plugin_name );

			$settings[ 'show_' . $plugin_name ] = 'yes';
			return $settings;
		}

		/**
		 * Plugin action to show / hide plugin settings and features.
		 */
		public function plugin_action() {
			$settings    = wptravel_get_settings();
			$plugin_name = $this->plugin_name;

			$plugin_name_ucfirst  = ucfirst( $plugin_name );
			$plugin_name_lower    = strtolower( $plugin_name );
			$plugin_name_replaced = str_replace( ' ', '_', $plugin_name_lower );

			$field_name  = 'show_' . $plugin_name_replaced;
			$field_label = $plugin_name_ucfirst;

			$field_value = isset( $settings[ $field_name ] ) ? $settings[ $field_name ] : 'yes'; ?>
			<table class="form-table">
				<tr>
					<th>
						<label for="<?php echo esc_attr( $field_name ); ?>"><?php echo esc_html( $field_label ); ?></label>
					</th>
					<td>
						<span class="show-in-frontend checkbox-default-design">
							<label data-on="ON" data-off="OFF">
								<input value="no" name="<?php echo esc_attr( $field_name ); ?>" type="hidden" />
								<input <?php checked( $field_value, 'yes' ); ?> value="yes" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>" type="checkbox" />
								<span class="switch"></span>
							</label>
						</span>
						<p class="description"><label for="<?php echo esc_attr( $field_name ); ?>">
						<?php
						// translators: For fiel label.
						sprintf( esc_html_e( 'Show all your "%s" settings and enable its feature.', 'wp-travel' ), $field_label );
						?>
						</label></p>
					</td>
				</tr>
			</table>
			<?php
		}
	}
}
