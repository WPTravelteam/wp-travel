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
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   1.0.0
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
					<?php wp_list_comments( apply_filters( 'wp_travel_review_list_args', array( 'callback' => 'wptravel_comments' ) ) ); ?>
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

				<p class="wp-travel-noreviews"><?php echo esc_html( apply_filters( 'wp_travel_single_archive_no_review_message', __( 'There are no reviews yet.', 'wp-travel' ) ) ); ?></p>

			<?php endif; ?>
		</div>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
				$commenter = wp_get_current_commenter();

				$comment_form = array(
					'title_reply'          => have_comments() ? apply_filters( 'wp_travel_single_archive_page_form_add_txt', __( 'Add a review', 'wp-travel' ) ) : __( 'Be the first to review &ldquo;', 'wp-travel' ).get_the_title().'&rdquo;',
					'title_reply_to'       => __( 'Leave a Reply to ', 'wp-travel' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp-travel' ) . ' <span class="required">*</span></label> ' .
									'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp-travel' ) . ' <span class="required">*</span></label> ' .
									'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
					),
					'label_submit'         => apply_filters( 'wp_travel_single_archive_comment_form_submit', __(  'Submit', 'wp-travel' ) ),
					'logged_in_as'         => '',
					'comment_field'        => '',
				);


				$comment_form['must_log_in'] = '<p class="must-log-in">' . __( 'You must be ', 'wp-travel' ).'<a href="'. esc_url( wp_login_url() ).'?redirect_to='.esc_url( get_the_permalink() ).'">'. __( 'logged in', 'wp-travel' ) .'</a>' . __( ' to post a review.', 'wp-travel' ) . '</p>';
				$settings                       = wptravel_get_settings();

				if ( is_user_logged_in() ) {
					global $current_user;

					if ( $settings['disable_admin_review'] == 'no' ) {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . apply_filters( 'wp_travel_single_archive_your_rating',  __( 'Your rating', 'wp-travel' ) ) . '</label><div id="wp-travel_rate" class="clearfix">
									<a href="#" class="rate_label far fa-star" data-id="1"></a>
									<a href="#" class="rate_label far fa-star" data-id="2"></a>
									<a href="#" class="rate_label far fa-star" data-id="3"></a>
									<a href="#" class="rate_label far fa-star" data-id="4"></a>
									<a href="#" class="rate_label far fa-star" data-id="5"></a>
								</div>
								<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . apply_filters( 'wp_travel_single_archive_your_review', __( 'Your review', 'wp-travel' ) ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
					}else{
						if ( !in_array( get_user_by('login', $current_user->user_login )->roles[0], array( 'administrator', 'editor', 'author' )) ) { 
							$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . apply_filters( 'wp_classified_single_archive_rating', __( 'Your rating', 'wp-travel' ) ) . '</label><div id="wp-travel_rate" class="clearfix">
								<a href="#" class="rate_label far fa-star" data-id="1"></a>
								<a href="#" class="rate_label far fa-star" data-id="2"></a>
								<a href="#" class="rate_label far fa-star" data-id="3"></a>
								<a href="#" class="rate_label far fa-star" data-id="4"></a>
								<a href="#" class="rate_label far fa-star" data-id="5"></a>
							</div>
							<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

							$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' .apply_filters( 'wp_travel_singel_archive_reviews',  __( 'Your review', 'wp-travel' ) ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
						}else{
							$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . apply_filters( 'wp_travel_single_archive_replys',  __( 'Your reply', 'wp-travel' ) ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
						}
					}
				}else{
					$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . apply_filters( 'wp_travel_single_archive_rate',  __( 'Your rating', 'wp-travel' ) ) . '</label><div id="wp-travel_rate" class="clearfix">
								<a href="#" class="rate_label far fa-star" data-id="1"></a>
								<a href="#" class="rate_label far fa-star" data-id="2"></a>
								<a href="#" class="rate_label far fa-star" data-id="3"></a>
								<a href="#" class="rate_label far fa-star" data-id="4"></a>
								<a href="#" class="rate_label far fa-star" data-id="5"></a>
							</div>
							<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

					$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . apply_filters( 'wp_travel_single_archive_rv',  __( 'Your review', 'wp-travel' ) ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
				}

				$recaptcha_site_key = !empty($settings['recaptcha_v2_site_key']) ? esc_attr($settings['recaptcha_v2_site_key']) : '';

				if (!empty($recaptcha_site_key)) {
                    $comment_form['comment_field'] .= '<div id="recaptcha2" class="g-recaptcha" data-sitekey="' . $recaptcha_site_key . '"></div>';
                }

				
				apply_filters( 'wp_travel_single_archive_comment_form', comment_form( apply_filters( 'wp_travel_product_review_comment_form_args', $comment_form ) ) );
				?>
			</div>
		</div>
	</div>

	<!-- <div class="clear"></div> -->
</div>

