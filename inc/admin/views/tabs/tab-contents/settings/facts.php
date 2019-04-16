<?php
/**
 * Callback for Facts tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_facts( $tab ) {
	if ( ! function_exists( 'wp_travel_trip_facts_setting_sample' ) ) {
		/**
		 * Wp_travel_trip_facts_setting_sample Facts layout.
		 *
		 * @since 1.3.2
		 */
		function wp_travel_trip_facts_setting_sample( $fact = false ) {
			ob_start();
			$str = random_int( 1, 1000000 );

			$i = $fact ? $str : '$index'; 
			$name = isset( $fact['name'] ) ? $fact['name'] : '';
			
			?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="headingOne">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" data-parent="#accordion-fact" href="#collapse-fact-<?php echo esc_attr( $i ); ?>" aria-expanded="true" aria-controls="collapse-fact-<?php echo esc_attr( $i ); ?>">
							<?php echo $name ? esc_html( $name ) : __( 'Your field name', 'wp-travel' ); ?>
							<span class="collapse-icon"></span>
							<span class="fact-remover" title="remove-table">
									<i class="dashicons dashicons-no-alt"></i>
								</span>
							
						</a>
					</h4>
				</div>
				<div id="collapse-fact-<?php echo esc_attr( $i ); ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
					<div class="panel-body">
						<div class="panel-wrap">
							<div class="wp-travel-fact-template-options form-table <?php echo ( ! $fact ) ? '' : 'open-table'; ?>">
								<!-- Fields -->
								<div class="form_field">
									<label class="label_title" for="wp_travel_trip_facts_settings_field_name"><?php echo esc_html__( 'Field Name', 'wp-travel' ); ?></label>
									<div class="subject_input">
										<input value="<?php echo esc_attr( $name ); ?>" id="wp_travel_trip_facts_settings_field_name" name="wp_travel_trip_facts_settings[<?php echo $i; ?>][name]" placeholder="<?php echo esc_attr( 'Enter field name', 'wp-travel' ); ?>" />
									</div>
								</div>
								<div class="form_field">
									<label class="label_title" for="wp_travel_trip_facts_settings_field_type"><?php echo esc_html__( 'Field Type', 'wp-travel' ); ?></label>
									<div class="subject_input">
										<select data-index="<?php echo $i; ?>" name="wp_travel_trip_facts_settings[<?php echo $i; ?>][type]" class="fact-type-changer wp-travel-select2" id="wp_travel_trip_facts_settings_field_type">
											<option value=""><?php echo esc_html( 'Select a type', 'wp-travel' ); ?></option>
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
											<option value="text" 
											<?php
											if ( isset( $fact['type'] ) && $fact['type'] == 'text' ) {
												echo 'selected';}
											?>
											><?php echo esc_html( 'Plain Text', 'wp-travel' ); ?></option>
										</select>
									</div>
								</div>
								<?php
								$display_tr = '';
								if ( ! $fact || ( isset( $fact['type'] ) && ! in_array( $fact['type'], array( 'single', 'multiple' ) ) ) ) {
									$display_tr = 'style="display:none;"';
								}
								?>
								<div class="form_field toggle-row multiple-val-<?php echo $i; ?>" <?php echo $display_tr; ?>>
									<label class="label_title" for="wp_travel_trip_facts_settings_field_values"><?php echo esc_html__( 'Values', 'wp-travel' ); ?></label>
									<div class="subject_input">
										<div class="fact-options">
											<input value=""  name="wp_travel_trip_facts_settings[<?php echo $i; ?>][options]" class="fact-options-list"  placeholder="<?php echo esc_attr( 'Add an option and press "Enter"', 'wp-travel' ); ?>"/>
											<div class="options-holder">
												<?php if ( isset( $fact['options'] ) && is_array( $fact['options'] ) ) : ?>
													<?php foreach ( $fact['options'] as $option ) : ?>
													<p><?php echo $option; ?><input type="hidden" name="wp_travel_trip_facts_settings[<?php echo $i; ?>][options][]" value="<?php echo $option; ?>"/><span class="option-deleter"><span class="dashicons dashicons-no-alt"></span></span></p>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
								<div class="form_field">
									<label class="label_title" for="wp_travel_trip_facts_settings_icon_class"><?php echo esc_html__( 'Icon Class', 'wp-travel' ); ?></label>
									<div class="subject_input">
										<input value="<?php echo isset( $fact['icon'] ) ? $fact['icon'] : ''; ?>" name="wp_travel_trip_facts_settings[<?php echo $i; ?>][icon]" placeholder="<?php esc_html_e( 'Icon', 'wp-travel' ); ?>"/>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	$settings                    = wp_travel_get_settings();
	$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';

	?>

	<div class="form_field">
		<label class="label_title" for="wp_travel_trip_facts_enable"><?php esc_html_e( 'Trip Facts', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="wp_travel_trip_facts_enable" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $wp_travel_trip_facts_enable ); ?> name="wp_travel_trip_facts_enable" id="wp_travel_trip_facts_enable" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="wp_travel_trip_facts_enable">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><?php esc_html_e( 'Enable Trip Facts display on trip single page.', 'wp-travel' ); ?></figcaption>
		</div>
	</div>
	<div <?php echo 'yes' !== $wp_travel_trip_facts_enable ? 'style="display:none"' : ''; ?> id="fact-app">
		<div id="wp-travel-fact-global-accordion" class="fact-global-accordion tab-accordion">
			<div class="panel-group" id="fact-sample-collector" role="tablist" aria-multiselectable="true">
				<!--  Repeator -->
				<?php if ( isset( $settings['wp_travel_trip_facts_settings'] ) && is_array( $settings['wp_travel_trip_facts_settings'] ) && count( $settings['wp_travel_trip_facts_settings'] ) > 0 ) : ?>
					<?php foreach ( $settings['wp_travel_trip_facts_settings'] as $fact ) : ?>
						<?php echo wp_travel_trip_facts_setting_sample( $fact ); ?>
					<?php endforeach; ?>
				<?php endif; ?>

			</div>
		</div>
		
		<button type="button" class="new-fact-setting-adder"><?php echo esc_html( 'Add new', 'wp-travel' ); ?></button>
		
		<div id="sampler" style="display:none">
			<?php echo wp_travel_trip_facts_setting_sample(); ?>
		</div>
	</div>

	<!-- ends -->
	<?php
}
