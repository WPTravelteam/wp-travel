<?php
/**
 * Fact Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

if ( ! function_exists( 'wp_travel_trip_callback_trip_facts' ) ) {
	function wp_travel_trip_callback_trip_facts() {
		global $post;

		$settings = wp_travel_get_settings();
		
		$wp_travel_trip_facts = get_post_meta( $post->ID, 'wp_travel_trip_facts', true );
		
		if ( is_string( $wp_travel_trip_facts ) ) {
		
			$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts, true );
		}
		
		
		
		if ( isset( $settings['wp_travel_trip_facts_settings'] ) && count( $settings['wp_travel_trip_facts_settings'] ) > 0 ) {
		
			$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';
		
			if ( 'no' === $wp_travel_trip_facts_enable ) {
				$settings_url = site_url( 'wp-admin/edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-facts' );
				printf( __( 'Trip facts are disabled in settings currently. Click %1$1shere%2$2s to enable', 'wp-travel' ), '<a href="' . $settings_url . '"', '</a>' );
			}
		
			if ( '' !== $wp_travel_trip_facts ) {
				?>
				<div class="form-table fact-table">	
					<div id="tab-accordion" class="tab-accordion wp-travel-accordion has-handler">
						<div data-factssettings='<?php echo isset( $settings['wp_travel_trip_facts_settings'] ) ? wp_json_encode( array_values( $settings['wp_travel_trip_facts_settings'] ) ) : '[]'; ?>' class="panel-group wp-travel-sorting-tabs ui-sortable" id="accordion-fact-data" role="tablist" aria-multiselectable="true">
							<?php
							if ( is_array( $wp_travel_trip_facts ) ) :
								foreach ( $wp_travel_trip_facts as $key => $fact ) :
									// Saved Facts.
									echo wp_travel_trip_facts_single_html( $fact, $key );
								endforeach;
							endif;
							?>
						</div>
					</div>
				</div>
		
				<?php
			}
			?>
			<div class="wp-travel-fact-quest-button clearfix">
				<input type="button" value="<?php echo esc_html( 'Add Fact', 'wp-travel' ); ?>" class="wp-travel-trip-facts-add-new button button-primary">
			</div>
			<?php
			$fact_settings = isset( $settings['wp_travel_trip_facts_settings'] ) ? array_values( $settings['wp_travel_trip_facts_settings'] ) : array();
			?>
			<script type="text/html" id="tmpl-wp-travel-trip-facts-options">
				<div class="panel panel-default ">
					<div class="panel-heading" role="tab" id="heading-{{data.random}}">
						<h4 class="panel-title">
							<div class="wp-travel-sorting-handle"></div>
							<a class="collapse in" role="button" data-toggle="collapse" data-parent="#accordion-fact-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">
								<span bind="fact_question_{{data.random}}"><?php echo esc_html__( 'Fact', 'wp-travel' ); ?><span>
								<!-- <span class="collapse-icon"></span> -->
							</a>
							<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
						</h4>
					</div>
					<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}
						<div class="panel-body">
							<div class="panel-wrap">
								<table class="form-table">
									<tbody>
										<tr>
											<th>
												<label for="fact_type_{{data.random}}" ><?php esc_html_e( 'Select Type', 'wp-travel' ); ?></label>
											</th>
											<td>
												<select id="fact_type_{{data.random}}" required class="fact-type-selecter" data-index="{{data.random}}" name="wp_travel_trip_facts[{{data.random}}][label]">
													<option value=""><?php esc_html_e( 'Select a Label', 'wp-travel' ); ?></option>  
												<?php foreach ( $fact_settings as $key => $setting ) : ?>
														<option 
														<?php
														if ( isset( $type ) && $type == $setting['name'] ) :
															$fact_settings = $setting;
															$selected      = $setting['type'];
															?>
															selected <?php endif; ?> value="<?php echo $setting['name']; ?>"><?php echo esc_html( $setting['name'] ); ?></option>
													<?php endforeach; ?>
												</select>
											</td>
										</tr>
										<tr class="fact-holder faq-question-text">
											<th>
												<label><?php echo esc_html( 'Value', 'wp-admin' ); ?></label>
											</th>
											<td class="fact-{{data.random}}">
		
											</td>
											<input type="hidden" class="icon-{{data.random}}" name="wp_travel_trip_facts[{{data.random}}][icon]" id="" value="">
											<input type="hidden"  class="type-{{data.random}}" name="wp_travel_trip_facts[{{data.random}}][type]" id="" value="">
										</tr >
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</script>
			<?php
		} else {
			$settings_url = site_url( 'wp-admin/edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-facts' );
			printf( __( ' %1$1sThere are no labels set currently. %2$2sAdd label%3$3s.%4$4s', 'wp-travel' ),  '<p id="pass-strength-result" class="good"><i class="fas fa-exclamation-triangle"></i>', '<a href="' . $settings_url . '">', '</a>', '</p>' );
		}
		
	}
}


/**
 * Fact Type Single.
 */
function wp_travel_fact_single( $fact, $index, $setting = array() ) {
	?>
	<select name="wp_travel_trip_facts[<?php echo $index; ?>][value]" id="">
		<?php
		if ( isset( $setting['options'] ) && is_array( $setting['options'] ) && count( $setting['options'] ) > 0 ) {
			foreach ( $setting['options'] as $option ) :
				?>
				<option 
				<?php
				if ( isset( $fact['value'] ) && $option == $fact['value'] ) {
					echo 'selected';}
				?>
				  value="<?php echo $option; ?>"><?php echo $option; ?></option>
				<?php
			endforeach;
		}
		?>
	</select>
	<?php
}
/**
 * Fact type Multiple.
 */
function wp_travel_fact_multiple( $fact, $index, $setting = array() ) {
	if ( isset( $setting['options'] ) && is_array( $setting['options'] ) ) {

		foreach ( $setting['options'] as $option ) :
			?>
			<label style="display:block; margin-top:5px; margin-bottom:5px;">
				<input type="checkbox" 
				<?php
				if ( isset( $fact['value'] ) && is_array( $fact['value'] ) && in_array( $option, $fact['value'] ) ) {
					echo 'checked';}
				?>
				 name="wp_travel_trip_facts[<?php echo $index; ?>][value][]" value="<?php echo $option; ?>" id="" /><?php echo $option; ?>
			</label>
			<?php
		endforeach;
	} else {
		echo esc_html( 'Please set value for this fact first.', 'wp-travel' );
	}
}

/**
 * Fact Type Text.
 */
function wp_travel_fact_text( $fact, $index, $setings = array() ) {
	?>
		<input type="text" name="wp_travel_trip_facts[<?php echo $index; ?>][value]" id="" value="<?php echo $fact['value']; ?>">
	<?php
}
/**
 * Fact Defaults.
 */
function wp_travel_fact_defaults( $fact, $index, $settings = array() ) {
	?>
	<input type="hidden" class="icon-<?php echo esc_attr( $index ); ?>" name="wp_travel_trip_facts[<?php echo $index; ?>][icon]" id="" value="<?php echo $settings['icon']; ?>">
	<input type="hidden"  class="type-<?php echo esc_attr( $index ); ?>" name="wp_travel_trip_facts[<?php echo $index; ?>][type]" id="" value="<?php echo $settings['type']; ?>">

	<?php
}
/**
 * Wp_travel_trip_facts_single_html Trips facts single html.
 *
 * @param array $fact.
 * @param int   $index.
 */
function wp_travel_trip_facts_single_html( $fact = array(), $index = false ) {
	$settings = wp_travel_get_settings();
	$settings = isset( $settings['wp_travel_trip_facts_settings'] ) ? array_values( $settings['wp_travel_trip_facts_settings'] ) : array();

	if ( '' === $settings ) {
		return '';
	}
	$name = array();
	foreach ( $settings as $set ) {
		$name[] = $set['name'];
	}
	if ( isset( $fact['label'] ) && ! in_array( $fact['label'], $name ) ) {
		return '';
	} else {
		$label = $fact['label'];
	}

	ob_start();
	is_array( $fact ) && extract( $fact );

	?>
	<div class="panel panel-default ">
			<div class="panel-heading" role="tab" id="heading-<?php echo $index; ?>">
				<h4 class="panel-title">
					<div class="wp-travel-sorting-handle"></div>
					<a class="<?php $index && print_r( 'collapsed' ); ?>" role="button" data-toggle="collapse" data-parent="#accordion-fact-data" href="#collapse-<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $index; ?>">
						<span><?php echo esc_html( $fact['label'] ); ?> <span>
						<!-- <span class="collapse-icon"></span> -->
					</a>
					<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
				</h4>
			</div>
			<div id="collapse-<?php echo $index; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo $index; ?>">
				<div class="panel-body">
					<div class="panel-wrap">
						<table class="form-table">
							<tbody>
								<tr>
									<th>
										<label><?php esc_html_e( 'Select Type', 'wp-travel' ); ?></label>
									</th>
									<td>
										<select class="fact-type-selecter" data-index="<?php echo $index; ?>" name="<?php echo 'wp_travel_trip_facts[' . $index . '][label]'; ?>">
										<?php if ( ! isset( $type ) ) : ?>
											<option value=""><?php esc_html_e( 'Select a Label', 'wp-travel' ); ?></option>
										<?php endif; ?>  
										<?php foreach ( $settings as $key => $setting ) : ?>
												<option 
												<?php
												if ( isset( $label ) && $label == $setting['name'] ) :
													$settings = $setting;
													$selected = $setting['type'];
													?>
													 selected <?php endif; ?> value="<?php echo $setting['name']; ?>"><?php echo esc_html( $setting['name'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</td>
								</tr>
								<tr class="fact-holder faq-question-text">
									<th>
										<label><?php echo esc_html( 'Value', 'wp-admin' ); ?></label>
									</th>
									<td class="fact-<?php echo $index; ?>">
										<?php isset( $selected ) && call_user_func( 'wp_travel_fact_' . $selected, $fact, $index, $settings ); ?>
									</td>
									<?php call_user_func( 'wp_travel_fact_defaults', $fact, $index, $settings ); ?>
								</tr >
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
