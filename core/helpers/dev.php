<?php
/**
 * WP Travel Dev Mode
 *
 * @package core/helpers
 */

/**
 * WP Travel Dev mode function.
 *
 * @return void
 */
function wptravel_dev_mode() {
    if ( defined( 'WPTRAVEL_DEV_MODE' ) ) {
        return WPTRAVEL_DEV_MODE;
    }
    return false;
}
wptravel_dev_mode();