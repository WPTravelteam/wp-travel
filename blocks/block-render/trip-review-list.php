<?php
/**
 * 
 * Render Callback For Trip Reivew List
 * 
 */

function wptravel_block_trip_review_list_render( $attributes = '' ) {
	$settings = wptravel_get_settings();
	ob_start();
	?>
    <div id="wptravel-block-trip-reviews-list" class="wptravel-block-wrapper wptravel-block-trip-reviews-list">
		<div id="reviews">
			<div id="comments clearfix">		
				<div class="wp-tab-review-inner-wrapper">

					<?php 
					$args              = array(
						'post_id' => get_the_ID()
					);
					$the_query = new WP_Comment_Query($args);
			
					if ( count( $the_query->comments ) > 0 ) : 
					?>

						<ol class="commentlist">
							<?php foreach( $the_query->comments as $comment ){ 
							$rating = intval( get_comment_meta( $comment->comment_ID, '_wp_travel_rating', true ) );	
							?>
								
							<li id="li-comment-<?php echo esc_attr( $comment->comment_ID ); ?>">

								<div id="comment-<?php echo esc_attr( $comment->comment_ID ); ?>" class="comment_container">

									<?php echo get_avatar( $comment, apply_filters( 'wp_travel_review_gravatar_size', '60' ), '' ); ?>

									<div class="comment-text">
										<!-- since 6.2 -->
										<?php
										if ( $settings['disable_admin_review'] == 'yes' ) :

											if ( get_user_by( 'login', $comment->comment_author ) ) {
												if ( in_array( get_user_by( 'login', $comment->comment_author )->roles[0], array( 'administrator', 'editor', 'author' ) ) ) {
													?>
													<div class="wp-travel-admin-review">
														<?php _e( 'Admin', 'wp-travel-block-pro' ); ?>
													</div>
													<?php
												} else {
													?>
													<div class="wp-travel-average-review" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-travel-block-pro' ), $rating ); ?>">
														<a>
														<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'wp-travel-block-pro' ); ?></span>
														</a>
													</div>
													<?php
												}
											} else {
												?>
												<div class="wp-travel-average-review" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-travel-block-pro' ), $rating ); ?>">
													<a>
													<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'wp-travel-block-pro' ); ?></span>
													</a>
												</div>
											<?php	} ?>
											
											<?php else : ?>
												<div class="wp-travel-average-review" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-travel-block-pro' ), $rating ); ?>">
													<a>
													<span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'wp-travel-block-pro' ); ?></span>
													</a>
												</div>
										<?php endif ?>

										<?php do_action( 'wp_travel_review_before_comment_meta', $comment ); ?>

										<?php if ( $comment->comment_approved == '0' ) : ?>

											<p class="meta"><em><?php esc_html_e( apply_filters( 'wp_travel_single_archive_comment_approve_message', 'Your comment is awaiting approval' ), 'wp-travel-block-pro' ); ?></em></p>

										<?php else : ?>

											<p class="meta">
												<strong><?php echo apply_Filters( 'wp_travel_single_archive_comment_author', $comment->comment_author ); ?></strong>&ndash; <time datetime="<?php echo apply_filters( 'wp_travel_single_archive_comment_date', get_comment_date( 'c', $comment->comment_ID ) ); ?>"><?php echo apply_filters( 'wp_travel_single_archive_comment_date_format', get_comment_date( get_option( 'date_format' ), $comment->comment_ID ) ); ?></time>
											</p>

										<?php endif; ?>

										<?php do_action( 'wp_travel_review_before_comment_text', $comment ); ?>

										<div class="description"><?php echo apply_filters( 'wp_travel_single_archive_comment', $comment->comment_content ); ?></div>
										<div class="reply">
										<?php
										do_action( 'wp_travel_single_archive_after_comment_text', $comment, $rating );
										// Reply Link.
										$post_id = get_the_ID();
										if ( ! comments_open( get_the_ID() ) ) {
											return;
										}
										global $user_ID;
										$login_text = __( 'please login to review', 'wp-travel-block-pro' );
										$link       = '';
										if ( get_option( 'comment_registration' ) && ! $user_ID ) {
											$link = '<a rel="nofollow" href="' . wp_login_url( get_permalink() ) . '">' . $login_text . '</a>';
										} else {

											$link = "<a class='comment-reply-link' href='" . esc_url( add_query_arg( 'replytocom', $comment->comment_ID ) ) . '#respond' . "' onclick='return addComment.moveForm(\"comment-$comment->comment_ID\", \"$comment->comment_ID\", \"respond\", \"$post_id\")'>" . esc_html( 'Reply', 'wp-travel-block-pro' ) . '</a>';
										}
										echo apply_filters( 'wp_travel_comment_reply_link', $link );
										?>
										</div>
										<?php do_action( 'wp_travel_review_after_comment_text', $comment ); ?>

									</div>
								</div>
							</li>
							<?php } ?>
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

						<p class="wp-travel-noreviews"><?php esc_html_e( apply_filters( 'wp_travel_single_archive_no_review_message', 'There are no reviews yet.' ), 'wp-travel-block-pro' ); ?></p>

					<?php endif; ?>
				</div>

				<div id="review_form_wrapper">
					<div id="review_form">
						<?php
						$commenter = wp_get_current_commenter();

						$comment_form = array(
							'title_reply'          => count( $the_query->comments ) > 0 ? __( apply_filters( 'wp_travel_single_archive_page_form_add_txt', 'Add a review' ), 'wp-travel-block-pro' ) : sprintf( __( 'Be the first to review &ldquo;%s&rdquo;', 'wp-travel-block-pro' ), get_the_title() ),
							'title_reply_to'       => __( 'Leave a Reply to %s', 'wp-travel-block-pro' ),
							'comment_notes_before' => '',
							'comment_notes_after'  => '',
							'fields'               => array(
								'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', 'wp-travel-block-pro' ) . ' <span class="required">*</span></label> ' .
											'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></p>',
								'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'wp-travel-block-pro' ) . ' <span class="required">*</span></label> ' .
											'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
							),
							'label_submit'         => __( apply_filters( 'wp_travel_single_archive_comment_form_submit', 'Submit' ), 'wp-travel-block-pro' ),
							'logged_in_as'         => '',
							'comment_field'        => '',
						);


						$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( __( 'You must be <a href="%1s">logged in</a> to post a review.', 'wp-travel-block-pro' ), esc_url( wp_login_url() ) ) . '</p>';
						$settings                       = wptravel_get_settings();

						if ( is_user_logged_in() ) {
							global $current_user;

							if ( $settings['disable_admin_review'] == 'no' ) {
								$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . __( apply_filters( 'wp_travel_single_archive_your_ratting', 'Your ratting' ), 'wp-travel-block-pro' ) . '</label><div id="wp-travel_rate" class="clearfix">
											<a href="#" class="rate_label far fa-star" data-id="1"></a>
											<a href="#" class="rate_label far fa-star" data-id="2"></a>
											<a href="#" class="rate_label far fa-star" data-id="3"></a>
											<a href="#" class="rate_label far fa-star" data-id="4"></a>
											<a href="#" class="rate_label far fa-star" data-id="5"></a>
										</div>
										<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

								$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( apply_filters( 'wp_travel_single_archive_your_review', 'Your review' ), 'wp-travel-block-pro' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
							}else{
								if ( !in_array( get_user_by('login', $current_user->user_login )->roles[0], array( 'administrator', 'editor', 'author' )) ) { 
									$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . __( apply_filters( 'wp_classified_single_archive_ratting', 'Your ratting' ), 'wp-travel-block-pro' ) . '</label><div id="wp-travel_rate" class="clearfix">
										<a href="#" class="rate_label far fa-star" data-id="1"></a>
										<a href="#" class="rate_label far fa-star" data-id="2"></a>
										<a href="#" class="rate_label far fa-star" data-id="3"></a>
										<a href="#" class="rate_label far fa-star" data-id="4"></a>
										<a href="#" class="rate_label far fa-star" data-id="5"></a>
									</div>
									<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

									$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( apply_filters( 'wp_travel_singel_archive_reviews', 'Your review' ), 'wp-travel-block-pro' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
								}else{
									$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( apply_filters( 'wp_travel_single_archive_replys', 'Your reply' ), 'wp-travel-block-pro' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
								}
							}
						}else{
							$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="wp_travel_rate_val">' . __( apply_filters( 'wp_travel_single_archive_rate', 'Your ratting' ), 'wp-travel-block-pro' ) . '</label><div id="wp-travel_rate" class="clearfix">
										<a href="#" class="rate_label far fa-star" data-id="1"></a>
										<a href="#" class="rate_label far fa-star" data-id="2"></a>
										<a href="#" class="rate_label far fa-star" data-id="3"></a>
										<a href="#" class="rate_label far fa-star" data-id="4"></a>
										<a href="#" class="rate_label far fa-star" data-id="5"></a>
									</div>
									<input type="hidden" value="0" name="wp_travel_rate_val" id="wp_travel_rate_val" ></p>';

							$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( apply_filters( 'wp_travel_single_archive_rv', 'Your review' ), 'wp-travel-block-pro' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
						}
						
						apply_filters( 'wp_travel_single_archive_comment_form', comment_form( apply_filters( 'wp_travel_product_review_comment_form_args', $comment_form ) ) );
						?>
					</div>
				</div>
			</div>

			<!-- <div class="clear"></div> -->
		</div>
	</div>
	<?php
	return ob_get_clean();
}