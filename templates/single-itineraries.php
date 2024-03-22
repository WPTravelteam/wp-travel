<?php
/**
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/single-itineraries.php.
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

if( !wp_is_block_theme() ){
	get_header( 'itinerary' ); ?>
	<?php 
	do_action( 'wp_travel_before_main_content' ); 
	?>
	<?php
	while ( have_posts() ) :
		the_post();

		if( !apply_filters( 'wp_travel_enable_trip_content', false ) ){
			do_action( 'wptravel_single_itinerary_main_content' );
		}
		
		if( apply_filters( 'wp_travel_enable_trip_content', false ) ){
			the_content();
		}

	endwhile; // end of the loop.
	?>
	<?php 
	do_action( 'wp_travel_after_main_content' ); 
	?>
	<?php
	get_footer( 'itinerary' );
	}else{
	?>

	<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo('charset'); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<?php
			$title = get_the_title();
			$site_name = get_bloginfo('name');
			$page_title = $title . ' - ' . $site_name;
			?>
			<title><?php echo esc_html( $page_title ); ?></title>
			<?php
			/*
			You have to run the do_blocks() between the <head></head> tags in order
			for WordPress to load the corresponding CSS.
			*/
			ob_start();
			block_header_area();
			$str = ob_get_clean();
			$block_header = do_blocks($str);
			// Spacer block.
			$str = '<div 
						style="height:32px" 
						aria-hidden="true" 
						class="wp-block-spacer"
					></div>';
			$block_spacer = do_blocks($str);
			// Content block.
			$block_content = do_blocks(
				'<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
				<!-- wp:post-content /-->
				</div>
				<!-- /wp:group -->'
			);
			ob_start();
			block_footer_area();
			$str = ob_get_clean();
			$block_footer = do_blocks($str);
			wp_head();
			?>
		</head>
		<body <?php body_class('block-theme-active'); ?>>
			<?php wp_body_open(); ?>
			<div class="wp-site-blocks">
				<header class="wp-block-template-part">
					<?php echo wp_kses_post( $block_header ); ?>
				</header>
				<main class="wptravel-content-wrapper is-layout-constrained" style="padding: 40px 20px 80px 20px;">
				<div class="wp-block-group alignwide">
				<?php
					do_action( 'wp_travel_before_main_content' ); 
					?>
					<?php
					while ( have_posts() ) :
						the_post();

						if( !apply_filters( 'wp_travel_enable_trip_content', false ) ){
							do_action( 'wptravel_single_itinerary_main_content' );
						}
						
						if( apply_filters( 'wp_travel_enable_trip_content', false ) ){
							the_content();
						}

					endwhile; // end of the loop.
					?>
					<?php 
					do_action( 'wp_travel_after_main_content' ); 
				?>
				</div>
				</main>
				<footer class="wp-block-template-part site-footer">
					<?php echo wp_kses_post( $block_footer ); ?>
				</footer>
			</div>
			<?php wp_footer(); ?>
		</body>
	</html>
	<?php 
	}