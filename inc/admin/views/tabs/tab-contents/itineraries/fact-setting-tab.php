<?php
/**
 * Facts Settinsgs Global.
 *
 * @package WP_Travel
 */

if ( ! function_exists( 'wp_travel_trip_facts_setting_sample' ) ) {
	/**
	 * Wp_travel_trip_facts_setting_sample Facts layout.
	 *
	 * @since 1.3.2
	 *
	 */
	function wp_travel_trip_facts_setting_sample( $fact = false ) {
		ob_start();
		$str = random_int( 1, 1000000 );
		?>

	<table class="form-table <?php echo ( ! $fact ) ? '' : 'open-table'; ?>">
	<tbody>
		<tr>
			<th>
				<?php echo esc_html( 'Field Name','wp-travel' ); ?>
			</th>
			<td>
				<input value="<?php echo isset($fact['name']) ? $fact['name'] : '' ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][name]" placeholder="<?php echo esc_attr( 'Enter field name', 'wp-travel' ); ?>" />
			</td>
		</tr>
		<tr class="toggle-row">
			<th>
				<?php echo esc_html( 'Field Type','wp-travel' ); ?>
			</th>
			<td>
				<select data-index="<?php echo $fact ? $str : '$index' ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][type]" class="fact-type-changer">
						<option value=""><?php echo esc_html( 'Select a type', 'wp-travel' ); ?></option>
						<option value="single" <?php if ( isset( $fact['type'] ) && $fact['type'] == 'single') echo 'selected'; ?>><?php echo esc_html( 'Single Select', 'wp-travel' ); ?></option>
						<option value="multiple" <?php if( isset( $fact['type']) && $fact['type'] == 'multiple' ) echo 'selected'; ?>><?php echo esc_html( 'Multiple Select', 'wp-travel' ); ?></option>
						<option value="text" <?php if( isset( $fact['type'] ) && $fact['type'] == 'text') echo 'selected'; ?>><?php echo esc_html( 'Plain Text', 'wp-travel' ); ?></option>
				</select>
			</td>
		</tr>
		<?php
		$display_tr = '';
		if ( ! $fact || ( isset( $fact['type'] ) && !in_array( $fact['type'],array( 'single','multiple' ) ) ) ) {
			$display_tr = 'style="display:none;"';
		}
		?>
		<tr class="toggle-row multiple-val-<?php echo $fact ? $str : '$index' ?>" <?php echo $display_tr; ?>>
			<th>
				<?php echo esc_html( 'Values','wp-travel' ); ?>
			</th>
			<td>
				<div class="fact-options">
					<input value=""  name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][options]" class="fact-options-list"  placeholder="<?php echo esc_attr( 'Add an option and press "Enter"', 'wp-travel' ); ?>"/>
					<div class="options-holder">
						<?php if ( isset( $fact['options'] ) && is_array( $fact['options'] ) ) : ?>
							<?php foreach ( $fact['options'] as $option ): ?>
							<p><?php echo $option; ?><input type="hidden" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][options][]" value="<?php echo $option; ?>"/><span class="option-deleter"><span class="dashicons dashicons-no-alt"></span></span></p>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</td>
		</tr>
		<tr class="toggle-row">
			<th>
				<?php echo esc_html( 'Icon Class','wp-travel' ); ?>
			</th>
			<td>
				<input value="<?php echo isset($fact['icon']) ? $fact['icon'] : '' ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][icon]" placeholder="<?php esc_html_e( 'Icon', 'wp-travel' ); ?>"/>
			</td>
		</tr>
		<tr class="open-close-row">
			<td colspan="2">
				<button type="button" class="fact-open" title="Toggle Table"><span class="dashicons <?php echo ( $fact ) ? 'dashicons-arrow-up' : 'dashicons-arrow-down'; ?>"></span></button>
			</td>
		</tr>
		<tr class="delete-row">
			<td colspan="2">
				<button type="button" class="fact-remover" title="remove-table"><span class="dashicons dashicons-no-alt"></span></button>
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
$settings = get_option('wp_travel_settings');

$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';

?>
<table class="form-table">
	<tr>
		<th><label for="wp_travel_trip_facts_enable"><?php esc_html_e( 'Trip Facts', 'wp-travel' ) ?></label></th>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input type="checkbox" value="yes" <?php checked( 'yes', $wp_travel_trip_facts_enable ) ?> name="wp_travel_trip_facts_enable" id="wp_travel_trip_facts_enable"/>
					<span class="switch">
				</span>
				</label>
			</span>
			<p class="description"><?php esc_html_e( 'Enable Trip Facts display on trip single page.', 'wp-travel' ) ?>
			</p>
		</td>
	</tr>
</table>
<div <?php  echo 'yes' !== $wp_travel_trip_facts_enable ? 'style="display:none"' : ''; ?> id="fact-app">
	<div id="sampler" style="display:none">
		<?php echo wp_travel_trip_facts_setting_sample(); ?>
	</div>
	<div id="fact-sample-collector">
		<?php if ( is_array( $settings ) && array_key_exists( 'wp_travel_trip_facts_settings', $settings ) ) : ?>
			<?php foreach ( $settings['wp_travel_trip_facts_settings'] as $fact ) : ?>
				<?php echo wp_travel_trip_facts_setting_sample( $fact ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<button type="button" class="new-fact-setting-adder"><?php echo esc_html( 'Add new', 'wp-travel' ); ?></button>
</div>
