<?php
/**
 * Trip Itineraries Tab Content.
 */


 function trip_callback_itineraries_content() {
	global $post;
	$date_format             = get_option( 'date_format' );
	$js_date_format          = wp_travel_date_format_php_to_js();
	$trip_itinerary_data_arr = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data' );
	$outline                 = get_post_meta( $post->ID, 'wp_travel_outline', true ); ?>
	
	<div class="form_field">
		<label class="label_title" for="content"><?php echo esc_html__( 'Outline', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<?php wp_editor( $outline, 'wp_travel_outline' ); ?>
		</div>		
	</div>
	
	
	<div id="tab-accordion-itineraries" class="tab-accordion">
		<div class="itinerary_block panel-group wp-travel-sorting-tabs" id="accordion-itinerary-data" role="tablist" aria-multiselectable="true">
			<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></h3>
	
			<?php
			if ( isset( $trip_itinerary_data_arr[0] ) && is_array( $trip_itinerary_data_arr[0] ) && count( $trip_itinerary_data_arr[0] ) != 0 ) :
				$empty_item_style    = 'display:none';
				$collapse_link_style = 'display:block';
			else :
				$empty_item_style    = 'display:block';
				$collapse_link_style = 'display:none';
			endif;
			?>
	
			<div class="while-empty" style="<?php echo esc_attr( $empty_item_style ); ?>">
				<p>
					<?php esc_html_e( 'Click on \'Add Itinerary\' to add Itineraries.', 'wp-travel' ); ?>
				</p>
			</div>
			<?php if ( isset( $trip_itinerary_data_arr[0] ) && ! empty( $trip_itinerary_data_arr[0] ) ) : ?>
				<?php $cnt = 0; ?>
				<?php foreach ( $trip_itinerary_data_arr[0] as $key => $itinerary ) : ?>
					<?php
	
					$itinerary_label = __( 'Untitled', 'wp-travel' );
					$itinerary_title = __( 'Untitled', 'wp-travel' );
					$itinerary_desc  = '';
					$itinerary_date  = '';
					$itinerary_time  = '';
					if ( isset( $itinerary['label'] ) && '' !== $itinerary['label'] ) {
						$itinerary_label = stripslashes( $itinerary['label'] );
					}
					if ( isset( $itinerary['title'] ) && '' !== $itinerary['title'] ) {
						$itinerary_title = stripslashes( $itinerary['title'] );
					}
					if ( isset( $itinerary['desc'] ) && '' !== $itinerary['desc'] ) {
						$itinerary_desc = stripslashes( $itinerary['desc'] );
					}
					if ( isset( $itinerary['date'] ) && '' !== $itinerary['date'] ) {
						$itinerary_date = stripslashes( $itinerary['date'] );
						// @since 1.8.3
						if ( ! empty( $itinerary_date ) && ! wp_travel_is_ymd_date( $itinerary_date ) ) {						
							$itinerary_date = wp_travel_format_ymd_date( $itinerary_date, $date_format );
						}
					}
					if ( isset( $itinerary['time'] ) && '' !== $itinerary['time'] ) {
						$itinerary_time = stripslashes( $itinerary['time'] );
					}
					?>
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="heading-itinerary-<?php echo esc_attr( $cnt ); ?>">
							<h4 class="panel-title">
								<div class="wp-travel-sorting-handle"></div>
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-itinerary-data" href="#collapse-itinerary-<?php echo esc_attr( $cnt ); ?>" aria-expanded="true" aria-controls="collapse-itinerary<?php echo esc_attr( $cnt ); ?>">
									<span bind="itinerary_label_<?php echo esc_attr( $cnt ); ?>" class="itinerary-label"><?php echo esc_html( $itinerary_label ); ?></span>,
									<span bind="itinerary_title_<?php echo esc_attr( $cnt ); ?>" class="itinerary-label"><?php echo esc_html( $itinerary_title ); ?></span>
									<span class="collapse-icon"></span>
								</a>
								<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
							</h4>
						</div>
						<div id="collapse-itinerary-<?php echo esc_attr( $cnt ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-itinerary-<?php echo esc_attr( $cnt ); ?>">
						<div class="panel-body">

							<div class="form_field">
								<label class="label_title" for="wp_travel_trip_itinerary_data_label"><?php echo esc_html__( 'Itinerary Label', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<input id="wp_travel_trip_itinerary_data_label" bind="itinerary_label_<?php echo esc_attr( $cnt ); ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $cnt ); ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
								</div>		
							</div>
							<div class="form_field">
								<label class="label_title" for="wp_travel_trip_itinerary_data_title"><?php echo esc_html__( 'Itinerary Title', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<input id="wp_travel_trip_itinerary_data_title" bind="itinerary_title_<?php echo esc_attr( $cnt ); ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $cnt ); ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
								</div>		
							</div>

							<div class="form_field">
								<label class="label_title" for="wp_travel_trip_itinerary_data_date"><?php echo esc_html__( 'Itinerary Date', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<input id="wp_travel_trip_itinerary_data_date" data-date-format="<?php echo esc_attr( $js_date_format ); ?>" class="wp-travel-datepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $cnt ); ?>][date]" value="<?php echo esc_html( $itinerary_date ); ?>">
								</div>		
							</div>

							<div class="form_field">
								<label class="label_title" for="wp_travel_trip_itinerary_data_time"><?php echo esc_html__( 'Itinerary Time', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<input id="wp_travel_trip_itinerary_data_time" class="wp-travel-timepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $cnt ); ?>][time]" value="<?php echo esc_html( $itinerary_time ); ?>">
								</div>		
							</div>

							<?php
							// @since 1.7.6 [ used in utilities to add itinerary images]
							do_action( 'wp_travel_itinerary_list_before_description', $cnt, $itinerary );
							?>
							<div class="wp-travel-itinerary" style="padding:10px">
								<?php $itinerary_desc = stripslashes( $itinerary_desc ); ?>
							</div>

							<div class="form_field">
								<label class="label_title" for="wp_travel_trip_itinerary_data_description"><?php echo esc_html__( 'Description', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<textarea id="wp_travel_trip_itinerary_data_description" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][desc]" ><?php echo wp_kses_post( $itinerary_desc ); ?></textarea>
								</div>		
							</div>
						</div>
						</div>
					</div>
					<?php $cnt++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="wp-travel-faq-quest-button">
		<button id="add_itinerary_row" class="button button-primary" >
			<?php _e( 'Add Itinerary', 'wp-travel' ); ?>
		</button>
	</div>
	
	<script type="text/html" id="tmpl-wp-travel-itinerary-items">
		<?php
			$uid             = '{{data.random}}';
			$itinerary_label = __( 'Day X', 'wp-travel' );
			$itinerary_title = __( 'Your Plan', 'wp-travel' );
		?>
			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $uid ); ?>">
					<h4 class="panel-title">
						<div class="wp-travel-sorting-handle"></div>
						<a role="button" data-toggle="collapse" data-parent="#accordion-itinerary-data" href="#collapse-<?php echo esc_attr( $uid ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $uid ); ?>">
	
						<span bind="itinerary_label_<?php echo esc_attr( $uid ); ?>" class="itinerary-label"><?php echo esc_html( $itinerary_label ); ?></span>,
						<span bind="itinerary_title_<?php echo esc_attr( $uid ); ?>" class="itinerary-label"><?php echo esc_html( $itinerary_title ); ?></span>
						<span class="collapse-icon"></span>
						</a>
						<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
					</h4>
				</div>
				<div id="collapse-<?php echo esc_attr( $uid ); ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $uid ); ?>">
				<div class="panel-body">
					<div class="form_field">
						<label class="label_title"><?php esc_html_e( 'Itinerary Label', 'wp-travel' ); ?></label>
						<div class="subject_input">
							<input bind="itinerary_label_<?php echo esc_attr( $uid ); ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
						</div>
					</div>
					<div class="form_field">
						<label class="label_title"><?php esc_html_e( 'Itinerary Title', 'wp-travel' ); ?></label>
						<div class="subject_input">
							<input bind="itinerary_title_<?php echo esc_attr( $uid ); ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
						</div>
					</div>
					<div class="form_field">
						<label class="label_title"><?php esc_html_e( 'Itinerary Date', 'wp-travel' ); ?></label>
						<div class="subject_input">
							<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" class="wp-travel-datepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $uid ); ?>][date]" value="">
						</div>
					</div>
					<div class="form_field">
						<label class="label_title"><?php esc_html_e( 'Itinerary Time', 'wp-travel' ); ?></label>
						<div class="subject_input">
							<input class="wp-travel-timepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $uid ); ?>][time]" value="">
						</div>
					</div>
					<?php
					// @since 1.7.6 [ used in utilities to add itinerary images]
					do_action( 'wp_travel_itinerary_list_template_before_description', $uid );
					?>
					<div class="form_field">
						<label class="label_title"><?php esc_html_e( 'Description', 'wp-travel' ); ?></label>
						<div class="subject_input">
							<textarea name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][desc]" ></textarea>
						</div>
					</div>
				</div>
				</div>
			</div>
	</script>
	<?php
 }

