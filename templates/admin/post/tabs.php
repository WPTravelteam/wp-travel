<?php
/**
 * Template file for WP Travel post tabs.
 *
 * @package WP Travel
 */

global $post;
$i = 0;
$tabs = WP_Travel_Admin_Post_Tabs::get_tabs( $args['post_type'] );
if ( empty( $tabs ) ) {
	return false;
}
?>
<div class="wp-travel-post-tabs-wrap">
	<ul class="wp-travel-post-tabs-nav">
		<?php
		foreach ( $tabs as $key => $tab ) :
			$class = ( 0 === $i ) ? 'wp-travel-post-tab-active' : '';
		?>
		<li id="wp-travel-post-tab-<?php echo esc_attr( $key ); ?>"><a href="#wp-travel-post-tab-content-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $tab['tab_label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="wp-travel-post-tabs-contents">
		<?php
		foreach ( $tabs as $key => $tab ) :
		?>
		<div id="wp-travel-post-tab-content-<?php echo esc_attr( $key ); ?>" class="ui-state-active wp-travel-post-tab-content">
			<h3 class="wp-travel-post-tab-content-title"><?php echo esc_attr( $tab['content_title'] ); ?></h3>
			<?php do_action( 'wp_travel_post_tabs_content_' . $key, $post ); ?>
		</div>
		<?php endforeach; ?>
	</div>
</div>
