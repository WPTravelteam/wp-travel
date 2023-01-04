<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
this is test

<a href="<?php echo esc_url( admin_url() ); ?>" class="obp-btn-link obp-hide"><?php esc_html_e( 'Return to dashboard', 'wp-travel' ); ?></a>