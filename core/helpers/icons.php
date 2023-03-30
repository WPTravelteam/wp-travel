<?php
/**
 * Helpers Icon class to get icons as per icon type.
 *
 * @package WP_Travel
 */

/**
 * WpTravel_Helpers_Icon class.
 */
class WpTravel_Helpers_Icon {

	/**
	 * Get Icon Html.
	 *
	 * @param array $args icon args.
	 */
	public static function get( $args ) {
		if ( ! $args ) {
			return;
		}
		ob_start();
		$icon_type = isset( $args['selected_icon_type'] ) ? $args['selected_icon_type'] : 'icon-class';
		switch ( $icon_type ) {
			case 'icon-class':
			case 'fontawesome-icon':
				$icon = $args['icon'];
				?><i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				<?php
				break;
			case 'custom-upload':
				$icon_url = $args['icon_img'];
				?>
				<img style="height:24px;margin:0;padding-right:10px" src="<?php echo esc_url( $icon_url ); ?>" />
				<?php
				break;
		}
		$content = ob_get_contents();
		ob_end_clean();
		$return = isset( $args['return'] ) ? $args['return'] : false;
		if ( $return ) {
			return $content;
		}
		echo $content; // @phpcs:ignore
	}
}
