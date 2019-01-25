<?php
/**
 * Display single wp travel reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/single-wp-travel-reviews.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews">
	<div id="comments clearfix">		
		<div class="wp-tab-review-inner-wrapper">

			<?php if ( have_comments() ) : ?>

				<ol class="commentlist">
					<?php wp_list_comments( apply_filters( 'wp_travel_review_list_args', array( 'callback' => 'wp_travel_comments' ) ) ); ?>
				</ol>

				<?php
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
					echo '<nav class="wp-travel-pagination">';
					paginate_comments_links(
						apply_filters(
							'wp_travel_comment_pagination_args',
							array(
								'prev_text' => '&larr;',
								'next_text' => '&rarr;',
								'type'      => 'list',
							)
						)
					);
					echo '</nav>';
				endif;
				?>

			<?php else : ?>

				<p class="wp-travel-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'wp-travel' ); ?></p>

			<?php endif; ?>
		</div>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
				$commenter = wp_get_current_commenter();

				$comment_form = array(
					'title_reply'          => have_comments() ? __( 'Add a review', 'wp-travel' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'wp-travel' ), get_the_title() ),
					'title_reply_to'       => __( 'Leave a Reply to %s', 'wp-travel' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp-travel' ) . ' <span class="required">*</span></label> ' .
									'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp-travel' ) . ' <span class="required">*</span></label> ' .
									'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
					),
					'label_submit'         => __( 'Submit', 'wp-travel' ),
					'logged_in_as'         => '',
					'comment_field'        => '',
				);


				$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a review.', 'wp-travel' ), esc_url( wp_login_url() ) ) . '</p>';

				// if ( get_option( 'wp-travel_enable_review_rating' ) === 'yes' ) {
					$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . __( 'Your Rating', 'wp-travel' ) . '</label><div id="wp-travel_rate" class="clearfix">
								<a href="#" class="rate_label dashicons dashicons-star-empty" data-id="1"></a>
								<a href="#" class="rate_label dashicons dashicons-star-empty" data-id="2"></a>
								<a href="#" class="rate_label dashicons dashicons-star-empty" data-id="3"></a>
								<a href="#" class="rate_label dashicons dashicons-star-empty" data-id="4"></a>
								<a href="#" class="rate_label dashicons dashicons-star-empty" data-id="5"></a>
							</div>
							<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';
				// }
				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( 'Your Review', 'wp-travel' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';

				comment_form( apply_filters( 'wp_travel_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	</div>

	<!-- <div class="clear"></div> -->
</div>


<?php

function wp_travel_comments( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	wp_travel_load_template(
		'review.php',
		array(
			'comment' => $comment,
			'args'    => $args,
			'depth'   => $depth,
		)
	);
}
