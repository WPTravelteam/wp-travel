<?php
$bookings = $args['bookings'];
?>
  <div class="my-order">
    <?php if ( ! empty( $bookings ) && is_array( $bookings ) ) : ?>
      <div class="view-order">
        <div class="order-list">
          <div class="order-wrapper">
            <h3><?php esc_html_e( 'Your Bookings', 'wp-travel' ); ?></h3>
            <div class="table-wrp">
              <table class="order-list-table">
                <thead>
                  <tr>
                    <th><?php esc_html_e( 'Trip', 'wp-travel' ); ?></th>
                    <th><?php esc_html_e( 'Contact Name', 'wp-travel' ); ?></th>
                    <th><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></th>
                    <th><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></th>
                    <th><?php esc_html_e( 'Payment Mode', 'wp-travel' ); ?></th>
                    <th class="text-right"><?php esc_html_e( 'Total Price', 'wp-travel' ); ?></th>
                    <th class="text-right"><?php esc_html_e( 'Paid', 'wp-travel' ); ?></th>
                    <?php do_action( 'wp_travel_dashboard_booking_table_title_after_paid' );  ?>
                  </tr>
                </thead>
                <tbody>
                <?php
                foreach ( $bookings as $key => $b_id ) :

                  $bkd_trip_id = get_post_meta( $b_id, 'wp_travel_post_id', true );

                  if ( ! $bkd_trip_id ) {
                    continue;
                  }
                  
                  $payment_info = wp_travel_booking_data( $b_id );
                  
                  $booking_status = $payment_info['booking_status'];
                  $payment_status = $payment_info['payment_status'];
                  $payment_mode   = $payment_info['payment_mode'];
                  $total_price  	= $payment_info['total_price'];
                  $paid_amount 	  = $payment_info['paid_amount'];
                  $due_amount  	  = $payment_info['due_amount'];
                  
                  $ordered_data = get_post_meta( $b_id, 'order_data', true );
                  
                  $fname = isset( $ordered_data['wp_travel_fname_traveller'] ) ? $ordered_data['wp_travel_fname_traveller'] : '';
                  
                  if ( '' !== $fname && is_array( $fname ) ) {
                    reset( $fname );
                    $first_key = key( $fname );
                   
                    $fname = isset( $fname[ $first_key ][0] ) ? $fname[ $first_key ][0] : '';
                  } else {
                    $fname = isset( $ordered_data['wp_travel_fname'] ) ? $ordered_data['wp_travel_fname'] : '';
                  }
                  
                  $lname = isset( $ordered_data['wp_travel_lname_traveller'] ) ? $ordered_data['wp_travel_lname_traveller'] : '';
                  
                  if ( '' !== $lname && is_array( $lname ) ) {
                    reset( $lname );
                    $first_key = key( $lname );
                   
                    $lname = isset( $lname[ $first_key ][0] ) ? $lname[ $first_key ][0] : '';
                  } else {
                    $lname = isset( $ordered_data['wp_travel_lname'] ) ? $ordered_data['wp_travel_lname'] : '';
                  }                  
                ?>
                  <tr class="tbody-content">

                    <td class="name" data-title="name">
                      <div class="name-title">
                          <a href="<?php echo esc_url( get_the_permalink( $bkd_trip_id ) ); ?>"><?php echo esc_html( get_the_title( $bkd_trip_id ) ); ?></a>
                      </div>
                    </td>

                    <td class="c-name" data-title="Contact Name">
                      <div class="contact-title">
                          <?php echo esc_html( $fname . ' ' . $lname ); ?>
                      </div>
                    </td>

                    <td class="booking-status" data-title="Booking Status">
                      <div class="contact-title">
                          <?php echo esc_html( $booking_status ); ?>
                      </div>
                    </td>

                    <td class="payment-status" data-title="Payment Status">
                      <div class="contact-title">
                      <?php echo esc_html( $payment_status ); ?>
                      </div>
                    </td>

                    <td class="payment-mode" data-title="Payment Mode">
                      <div class="contact-title">
                          <?php echo esc_html( $payment_mode ); ?>
                          <?php do_action( 'wp_travel_dashboard_booking_after_payment_mode', $ordered_data, $payment_info );  ?>
                      </div>
                    </td>

                    <td class="product-subtotal text-right" data-title="Total">
                      <div class="order-list-table">
                        <p>
                          <strong>
                            <span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
                            <span class="wp-travel-trip-total"> <?php echo esc_html( $total_price ); ?> </span>
                          </strong>
                        </p>
                      </div>
                    </td>
                    <td class="product-subtotal text-right" data-title="Paid">
                      <div class="order-list-table">
                        <p>
                          <strong>
                            <span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
                            <span class="wp-travel-trip-total"> <?php echo esc_html( $paid_amount ); ?> </span>
                          </strong>
                        </p>
                      </div>
                    </td>
                    <?php do_action( 'wp_travel_dashboard_booking_table_content_after_paid', $ordered_data, $payment_info );  ?>

                  </tr>
                <?php
                endforeach;
                ?>
                </tbody>
                <tfoot>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
        <div class="book-more">
          <a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book more?', 'wp-travel' ); ?></a>
        </div>
      </div>
    <?php else : ?>
      <div class="no-order">
        <p>
          <?php esc_html_e( 'You have not booked any trips', 'wp-travel' ); ?>
          <a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book one now?', 'wp-travel' ); ?></a>
        </p>
      </div>
    <?php endif; ?>
  </div>
