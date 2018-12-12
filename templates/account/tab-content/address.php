<?php
$current_user    = $args['current_user'];
?>

  <div class="clearfix">
    <div class="payment-content">
      <div class="title">
        <h3><?php esc_html_e( 'Billing Address', 'wp-travel' ); ?></h3>
      </div>
      <?php
      echo wp_travel_get_template_html( 'account/form-edit-billing.php',
      array(
      'user'   => $current_user,
      ) );
    ?>
    </div>
  </div>
