<?php
/**
 * Template file for WP Travel locations tab.
 *
 * @package WP Travel
 */

/**
 * Callback for Facts tab.
 *
 * @param  Array $tab  List of tabs.
 */
function wptravel_settings_callback_facts( $tab ) {
	if ( ! $tab ) {
		return;
	}
	$settings                    = wptravel_get_settings();
	$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';

	?>
	<table class="form-table">
		<tr>
			<th><label for="wp_travel_trip_facts_enable"><?php esc_html_e( 'Trip Facts', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="" name="wp_travel_trip_facts_enable" type="hidden" />
						<input type="checkbox" value="yes" <?php checked( 'yes', $wp_travel_trip_facts_enable ); ?> name="wp_travel_trip_facts_enable" id="wp_travel_trip_facts_enable"/>
						<span class="switch">
					</span>
					</label>
				</span>
				<p class="description"><?php esc_html_e( 'Enable Trip Facts display on trip single page.', 'wp-travel' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<div <?php echo 'yes' !== $wp_travel_trip_facts_enable ? 'style="display:none"' : ''; ?> id="fact-app">
		<div id="sampler" style="display:none">
			<?php echo wptravel_trip_facts_setting_sample(); // @phpcs:ignore ?>
		</div>
		<div id="fact-sample-collector">
			<?php
			if ( is_array( $settings ) && array_key_exists( 'wp_travel_trip_facts_settings', $settings ) ) :
				foreach ( $settings['wp_travel_trip_facts_settings'] as $key => $fact ) :
					$fact['id'] = $key;
					echo wptravel_trip_facts_setting_sample( $fact ); // @phpcs:ignore
				endforeach;
			endif;
			?>
		</div>
		<button type="button" class="new-fact-setting-adder button"><?php echo esc_html__( 'Add new', 'wp-travel' ); ?></button>
	</div>

	<?php
}

if ( ! function_exists( 'wptravel_trip_facts_setting_sample' ) ) {

	/**
	 * Wp_travel_trip_facts_setting_sample Facts layout.
	 *
	 * @param Mixed $fact Fact array data or boolean.
	 *
	 * @since 1.3.2
	 */
	function wptravel_trip_facts_setting_sample( $fact = false ) {
		ob_start();
		$str = isset( $fact['id'] ) ? $fact['id'] : random_int( 1, 1000000 );
		?>

		<table class="form-table <?php echo ( ! $fact ) ? '' : 'open-table'; ?>">
			<tbody>
				<tr>
					<th>
						<label for="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_name"><?php echo esc_html( 'Field Name', 'wp-travel' ); ?></label>
					</th>
					<td>
						<input class="fact-title" data-initial-title="<?php echo ! empty( $fact['initial'] ) ? $fact['initial'] : $fact['name']; ?>" value="<?php echo isset( $fact['name'] ) ? $fact['name'] : ''; ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][name]" id="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_name" placeholder="<?php echo esc_attr( 'Enter field name', 'wp-travel' ); ?>" type="text" />
						<input type="hidden" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][initial]" value="<?php echo ! empty( $fact['initial'] ) ? $fact['initial'] : $fact['name']; ?>">
					</td>
				</tr>
				<tr class="toggle-row">
					<th>
						<label for="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_type"><?php echo esc_html( 'Field Type', 'wp-travel' ); ?></label>
					</th>
					<td>
						<?php
						$facttypes = array(
							'text'     => __( 'Plain Text', 'wp-travel' ),
							'single'   => __( 'Single Select', 'wp-travel' ),
							'multiple' => __( 'Multiple Select', 'wp-travel' ),
						);
						?>
						<?php if ( is_array( $fact ) ) : ?>
							<input type="hidden" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][type]" id="" value="<?php echo isset( $fact['type'] ) && array_key_exists( $fact['type'], $facttypes ) ? $fact['type'] : 'text'; ?>">
							<p style="height: 38px;line-height: 36px;width: 287px;background: #f9f9f9;padding: 0px 0 0px 10px;border: #ddd 1px solid;"><?php echo isset( $fact['type'] ) && array_key_exists( $fact['type'], $facttypes ) ? $facttypes[ $fact['type'] ] : ''; ?></p>
						<?php endif; ?>
						<?php if ( ! $fact ) : // remove type selector for older fact to prevent future issues @since 1.0.7 ?>
						<select data-index="<?php echo $fact ? $str : '$index'; ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][type]" id="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_type" class="fact-type-changer">
								<option value="text" 
								<?php
								if ( isset( $fact['type'] ) && $fact['type'] == 'text' ) {
									echo 'selected';}
								?>
								><?php echo esc_html( 'Plain Text', 'wp-travel' ); ?></option>
								<!-- <option value=""><?php echo esc_html( 'Select a type', 'wp-travel' ); ?></option> -->
								<option value="single" 
								<?php
								if ( isset( $fact['type'] ) && $fact['type'] == 'single' ) {
									echo 'selected';}
								?>
								><?php echo esc_html( 'Single Select', 'wp-travel' ); ?></option>
								<option value="multiple" 
								<?php
								if ( isset( $fact['type'] ) && $fact['type'] == 'multiple' ) {
									echo 'selected';}
								?>
								><?php echo esc_html( 'Multiple Select', 'wp-travel' ); ?></option>
						</select>
						<?php endif; ?>
					</td>
				</tr>
				<?php
				$display_tr = '';
				if ( ! $fact || ( isset( $fact['type'] ) && ! in_array( $fact['type'], array( 'single', 'multiple' ) ) ) ) {
					$display_tr = 'style="display:none;"';
				}
				?>
				<tr class="toggle-row multiple-val-<?php echo $fact ? $str : '$index'; ?>" <?php echo $display_tr; ?>>
					<th>
						<label for="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_options"><?php echo esc_html( 'Values', 'wp-travel' ); ?></label>
					</th>
					<td>
						<div class="fact-options">
							<input value=""  name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][options]"  class="fact-options-list"  placeholder="<?php echo esc_attr( 'Add an option and press "Enter"', 'wp-travel' ); ?>" type="text"/>
							<div class="options-holder">
								<?php if ( isset( $fact['options'] ) && is_array( $fact['options'] ) ) : ?>
									<?php foreach ( $fact['options'] as $option ) : ?>
									<p><input type="text" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][options][]" value="<?php echo $option; ?>"/><span class="option-deleter"><span class="dashicons dashicons-no-alt"></span></span></p>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
					</td>
				</tr>
				<tr class="toggle-row">
					<th>
						<label for="wp_travel_trip_facts_settings_<?php echo $fact ? $str : '$index'; ?>_icon"><?php echo esc_html( 'Icon Class', 'wp-travel' ); ?></label>
					</th>
					<td>
						<input value="<?php echo isset( $fact['icon'] ) ? $fact['icon'] : ''; ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][icon]"   placeholder="<?php esc_html_e( 'Icon', 'wp-travel' ); ?>" type="text"/>
					</td>
				</tr>
				<tr class="open-close-row">
					<td colspan="2">
						<button type="button" class="fact-open button" title="Toggle Table"><span class="dashicons <?php echo ( $fact ) ? 'dashicons-arrow-up' : 'dashicons-arrow-down'; ?>"></span></button>
					</td>
				</tr>
				<tr class="delete-row">
					<td colspan="2">
						<button type="button" class="fact-remover button" title="remove-table"><span class="dashicons dashicons-no-alt"></span></button>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
