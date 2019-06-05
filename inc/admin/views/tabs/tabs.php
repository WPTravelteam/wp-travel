<?php
/**
 * Template file for WP Travel tabs.
 *
 * @package WP Travel
 */

?>


<section class="wp-travel-tabs-wrap clearfix">
	<ul class="wp-travel-tabs-nav">
		<?php
		foreach ( $tabs as $key => $tab ) :
			$icon = isset( $tab['icon'] ) ? $tab['icon'] : 'fa-thumbtack';
			$class = ( 0 === $i ) ? 'wp-travel-tab-active' : '';
			$callback = isset( $tab['callback'] ) ? $tab['callback'] : ''; // just to display callback function.
		?>

		<li data-cb="<?php echo esc_attr( $callback ) ?>" id="wp-travel-tab-<?php echo esc_attr( $key ); ?>"  ><a href="#wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class ); ?>"><i class="fas <?php echo esc_attr( $icon ); ?>"></i><?php echo esc_attr( $tab['tab_label'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="wp-travel-tabs-contents">
		<?php
		foreach ( $tabs as $key => $tab ) :
		?>
		<div id="wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="ui-state-active wp-travel-tab-content">
			<h3 class="wp-travel-tab-content-title tab_content_title"><?php echo esc_attr( $tab['content_title'] ); ?></h3>
			<?php do_action( 'wp_travel_tabs_content_' . $collection . '_' . $key, $key, $args ); // @since 1.9.0 ?>
			<?php wp_travel_do_deprecated_action( 'wp_travel_tabs_content_' . $collection, array( $key, $args ), '2.0.4', 'wp_travel_tabs_content_' . $collection . '_' . $key ); ?>
		</div>
		<?php endforeach; ?>

	</div>
</section>
