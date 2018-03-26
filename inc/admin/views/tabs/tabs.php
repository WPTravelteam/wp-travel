<?php
/**
 * Template file for WP Travel tabs.
 *
 * @package WP Travel
 */

?>
<div class="wp-travel-tabs-wrap">
	<ul class="wp-travel-tabs-nav">
		<?php
		foreach ( $tabs as $key => $tab ) :
			$class = ( 0 === $i ) ? 'wp-travel-tab-active' : '';
		?>
		<li id="wp-travel-tab-<?php echo esc_attr( $key ); ?>"><a href="#wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $tab['tab_label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="wp-travel-tabs-contents">
		<?php
		foreach ( $tabs as $key => $tab ) :
		?>
		<div id="wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="ui-state-active wp-travel-tab-content">
			<h3 class="wp-travel-tab-content-title"><?php echo esc_attr( $tab['content_title'] ); ?></h3>
			<?php do_action( 'wp_travel_tabs_content_' . $collection, $key, $args ); ?>
		</div>
		<?php endforeach; ?>

	</div>
</div>
