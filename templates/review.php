<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/review.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
global $comment;


$rating   = intval( get_comment_meta( $comment->comment_ID, '_wp_travel_rating', true ) ); ?>

<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php echo get_avatar( $comment, apply_filters( 'wp_travel_review_gravatar_size', '60' ), '' ); ?>

		<div class="comment-text">
			<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="wp-travel-average-review" title="<?php echo sprintf( __( 'Rated %d out of 5', 'wp-travel' ), $rating ) ?>">
				<a>
				 <span style="width:<?php echo esc_attr( ( $rating / 5 ) * 100 ); ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'wp-travel' ); ?></span>
			    </a>
			</div>

			<?php do_action( 'wp_travel_review_before_comment_meta', $comment ); ?>

			<?php if ( $comment->comment_approved == '0' ) : ?>

				<p class="meta"><em><?php esc_html_e( 'Your comment is awaiting approval', 'wp-travel' ); ?></em></p>

			<?php else : ?>

				<p class="meta">
					<strong itemprop="author"><?php comment_author(); ?></strong>&ndash; <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>"><?php echo get_comment_date( get_option('date_format') ); ?></time>:
				</p>

			<?php endif; ?>

			<?php do_action( 'wp_travel_review_before_comment_text', $comment ); ?>

			<div itemprop="description" class="description"><?php comment_text(); ?></div>
			<div class="reply">
			<?php
			// Reply Link.
			$post_id  = get_the_ID();
			if ( ! comments_open( get_the_ID() ) ) {
				return;
			}
			global $user_ID;
			$login_text = __( 'please login to review', 'wp-travel' );
			$link = '';
			if ( get_option( 'comment_registration' ) && ! $user_ID ) {
				$link = '<a rel="nofollow" href="' . wp_login_url( get_permalink() ) . '">' . $login_text . '</a>';
			} else {

				$link = "<a class='comment-reply-link' href='" . esc_url( add_query_arg( 'replytocom', $comment->comment_ID ) ) . "#respond" . "' onclick='return addComment.moveForm(\"comment-$comment->comment_ID\", \"$comment->comment_ID\", \"respond\", \"$post_id\")'>".esc_html('Reply', 'wp-travel')."</a>";
			}
			echo $link; ?>
			</div>
			<?php do_action( 'wp_travel_review_after_comment_text', $comment ); ?>

		</div>
	</div>
