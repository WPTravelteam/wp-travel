
  <div class="log-out">
    <div class="title">
      <h3><?php esc_html_e( 'Log Out?', 'wp-travel' ); ?></h3>
      <span>
        <?php esc_html_e( 'Are you sure want to log out?', 'wp-travel' ); ?>
        <a href="<?php echo esc_url( wp_logout_url( wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ) ); ?>"><?php esc_html_e( 'Log Out', 'wp-travel' ); ?></a>
      </span>
    </div>

  </div>
