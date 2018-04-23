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
	function wp_travel_trip_facts_setting_sample($fact = false)
	{
		ob_start();
		$str = random_int(1, 1000000);
		?>

	<table class="form-table open-table">
	<tbody>
			<tr >
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
				<select name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][type]" class="fact-type-changer">
						<option><?php echo esc_html( 'Select a type', 'wp-travel' ); ?></option>
						<option value="single" <?php if ( isset( $fact['type'] ) && $fact['type'] == 'single') echo 'selected'; ?>><?php echo esc_html( 'single', 'wp-travel' ); ?></option>
						<option value="multiple" <?php if( isset( $fact['type']) && $fact['type'] == 'multiple' ) echo 'selected'; ?>><?php echo esc_html( 'multiple', 'wp-travel' ); ?></option>
						<option value="text" <?php if( isset( $fact['type'] ) && $fact['type'] == 'text') echo 'selected'; ?>><?php echo esc_html( 'text', 'wp-travel' ); ?></option>
				</select>
			</td>
		</tr>
			<tr class="toggle-row">
				<th>
					<?php echo esc_html( 'Enter value separeted','wp-travel' ); ?>
				</th>
				<td>
					<div class="fact-options" <?php if(!$fact || (isset($fact['type']) && !in_array($fact['type'],['single','multiple']))) : ?> style="display:none" <?php endif; ?>>
						<input value=""  name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index'; ?>][options]" class="fact-options-list"  placeholder="Enter values separeted by commas"/>
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
				<input value="<?php echo isset($fact['icon']) ? $fact['icon'] : '' ?>" name="wp_travel_trip_facts_settings[<?php echo $fact ? $str : '$index' ?>][icon]" placeholder="Icon"/>
			</td>
		</tr>
		<tr class="open-close-row">
			<td colspan="2">
				<button type="button" class="fact-open" title="Toggle Table"><span class="dashicons dashicons-arrow-down"></span></button>
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
?>

<div id="fact-app">
	<div id="sampler" style="display:none">
		<?php echo wp_travel_trip_facts_setting_sample(); ?>
	</div>
	<div id="fact-sample-collector">
		<?php if (array_key_exists('wp_travel_trip_facts_settings', $settings)) : ?>
			<?php foreach ($settings['wp_travel_trip_facts_settings'] as $fact) : ?>
				<?php echo wp_travel_trip_facts_setting_sample($fact); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<button type="button" class="new-fact-setting-adder">Add new</button>
</div>
<script>
jQuery(document).ready(function () {


	jQuery(document).on('click','.fact-open', function(){
		jQuery(this).parents('table').toggleClass('open-table');
		jQuery(this).toggleClass('hide');
	})



	jQuery(document).on('click', '.new-fact-setting-adder', function () {
		jQuery('#fact-sample-collector').append(jQuery('#sampler').html().split('$index').join(Math.random().toString(36).substring(2, 15)));
	});

	jQuery(document).on('click','.fact-remover', function(){
		confirm('Are you sure ?')&&jQuery(this).parent().parent().parent().parent().remove();
	});

	jQuery(document).on('change','.fact-type-changer', function(){
		const val = jQuery(this).find(':selected').val();
		const siblings = jQuery(this).siblings('.fact-options');
		['single','multiple'].includes(val) ? siblings.fadeIn() : siblings.fadeOut()
	})

	jQuery(document).on('click', '.option-deleter', function(){
		jQuery(this).parent().remove();
	})

	jQuery(document).on('keypress', '.fact-options-list', function(e){
		if(e.which == 13){
			e.preventDefault();
			if(jQuery(this).val() == ''){
return;
			}
			const val = jQuery(this).val();
			jQuery(this).val('')
			jQuery(this).siblings('.options-holder').append('<p>'+val+'<input type="hidden" name="'+jQuery(this).attr('name')+'[]" value="'+val+'"/><span class="option-deleter"><span class="dashicons dashicons-no-alt"></span></span></p>')
		}
	});
})
</script>
