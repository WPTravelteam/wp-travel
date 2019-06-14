<?php
/**
 * Template file for WP Travel inventory tab.
 *
 * @package WP Travel
 */

/**
 * Callback Function For Inventory Content Tabs
 *
 * @param string $tab  tab name 'inventory'.
 * @param array  $args arguments function arugments.
 * @return Mixed
 */
function wp_travel_trip_callback_faq( $tab, $args ) {

	do_action( 'wp_travel_utils_itinerary_global_faq_settings' );

	$post_id       = $args['post']->ID;
	$faq_questions = get_post_meta( $post_id, 'wp_travel_faq_question', true );

	dd( $faq_questions );

	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'       => __( 'Tired of updating repitative FAQs ?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can create and use Global FAQs in all of your trips !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
	?>
			
	<div class="wp-travel-tab-content-faq-header clearfix">
		<?php
		if ( is_array( $faq_questions ) && count( $faq_questions ) != 0 ) :
			$empty_item_style    = 'display:none';
			$collapse_link_style = 'display:block';
		else :
			$empty_item_style    = 'display:block';
			$collapse_link_style = 'display:none';
		endif;
		?>

		<div class="while-empty" style="<?php echo esc_attr( $empty_item_style ); ?>">
			<p>
				<?php esc_html_e( 'Click on add new question to add FAQ.', 'wp-travel' ); ?>
			</p>
		</div>
		<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_link_style ); ?>" >
			<a href="#" data-parent="wp-travel-tab-content-faq" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
			<a data-parent="wp-travel-tab-content-faq" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
		</div>
	</div>
	<div id="tab-accordion" class="tab-accordion wp-travel-accordion has-handler">
		<div class="panel-group wp-travel-sorting-tabs" id="accordion-faq-data" role="tablist" aria-multiselectable="true">
			<?php if ( is_array( $faq_questions ) && count( $faq_questions ) > 0 ) : ?>

				<?php $faq_answers = get_post_meta( $post_id, 'wp_travel_faq_answer', true ); ?>

				<?php foreach ( $faq_questions as $key => $question ) : ?>
					<?php $question = ( '' !== $question ) ? $question : __( 'Untitled', 'wp-travel' ); ?>
					<div class="panel panel-default">
						<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $key ); ?>">
							<h4 class="panel-title">
								<div class="wp-travel-sorting-handle"></div>
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-faq-data" href="#collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $key ); ?>">

									<span bind="faq_question_<?php echo esc_attr( $key ); ?>" class="faq-label"><?php echo esc_html( $question ); ?></span>

								<!-- <span class="collapse-icon"></span> -->
								</a>
								<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
							</h4>
						</div>
						<div id="collapse-<?php echo esc_attr( $key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>">
						<div class="panel-body">
							<div class="panel-wrap">
								<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
								<input bind="faq_question_<?php echo esc_attr( $key ); ?>" type="text" class="faq-question-text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" value="<?php echo esc_html( $question ); ?>">
							</div>
							<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."><?php echo esc_attr( $faq_answers[ $key ] ); ?></textarea>
						</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="wp-travel-faq-quest-button clearfix">
		<input type="button" value="Add New Question" class="button button-primary wp-travel-faq-add-new">
	</div>
	<script type="text/html" id="tmpl-wp-travel-faq">

		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="heading-{{data.random}}">
				<h4 class="panel-title">
					<div class="wp-travel-sorting-handle"></div>
					<a role="button" data-toggle="collapse" data-parent="#accordion-faq-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">

						<span bind="faq_question_{{data.random}}"><?php echo esc_html( 'FAQ?', 'wp-travel' ); ?></span>

					<!-- <span class="collapse-icon"></span> -->
					</a>
					<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
				</h4>
			</div>
			<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
				<div class="panel-body">
					<div class="panel-wrap">
						<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
						<input bind="faq_question_{{data.random}}" type="text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" class="faq-question-text" value="">
					</div>
					<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."></textarea>
				</div>
			</div>
		</div>
	</script>
	<?php
}

