<?php
/**
 * Itinerary Archive Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/archive-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see         http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     WP_Travel
 * @since       1.0.0
 */

if( !wp_is_block_theme() ){
	$template      = get_option( 'template' );
	$current_theme = wp_get_theme();
	$sanitized_get = WP_Travel::get_sanitize_request( 'get', true );
	$view_mode     = wptravel_get_archive_view_mode( $sanitized_get );
	get_header( 'itinerary' );


	if ( 'Divi' === $template ) {
		?>
		<div class="container clearfix">
		<?php
	}


	if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) ) {
		?> <div class="wrap"><?php
	}
	do_action( 'wp_travel_before_main_content' );
	?>
	<div id="wptravel-archive-wrapper" class="wptravel-archive-wrapper <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?> ">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
			endwhile; // end of the loop.
		else :
			wptravel_get_template_part( 'v2/content', 'archive-itineraries-none' );
		endif;
		?>
	</div>
	<?php
	do_action( 'wp_travel_after_main_content' );
	do_action( 'wp_travel_archive_listing_sidebar' );

	if ( 'twentyseventeen' === $current_theme->get( 'TextDomain' ) || 'Divi' === $template ) {
		?>
		</div>
		<?php
	}

	get_footer( 'itinerary' );

}else{
	$sanitized_get = WP_Travel::get_sanitize_request( 'get', true );
	$view_mode     = wptravel_get_archive_view_mode( $sanitized_get );
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
		<body <?php body_class( 'block-theme-active' ); ?>>
			<?php wp_body_open(); ?>
			<div class="wp-site-blocks">
				<header class="wp-block-template-part">
					<?php echo wp_kses_post( $block_header ); ?>
				</header>
				<main class="wptravel-content-wrapper is-layout-constrained" style="display:flex; padding: 60px 20px 80px 20px;">
				<div class="wp-block-group archive alignwide">
				<?php
					do_action( 'wp_travel_before_main_content' );
					?>
					
					<div id="wptravel-archive-wrapper" class="wptravel-archive-wrapper <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?> ">
						<?php
						if ( have_posts() ) :
							while ( have_posts() ) :
								the_post();
								wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
							endwhile; // end of the loop.
						else :
							wptravel_get_template_part( 'v2/content', 'archive-itineraries-none' );
						endif;
						do_action( 'wp_travel_after_main_content' );
						?>
						
					</div>
					<?php
					
					do_action( 'wp_travel_archive_listing_sidebar' ); 
					
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