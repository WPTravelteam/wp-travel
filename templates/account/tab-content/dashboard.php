<?php
$bookings_glance    = $args['bookings_glance'];
$biling_glance_data = $args['biling_glance_data'];
?>
<div>
  <p><?php esc_html_e( 'Hello, ', 'wp-travel' ); ?><strong><?php echo esc_html( $current_user->display_name ); ?></strong></p>
  <p><?php esc_html_e( 'From your account dashboard you can view your recent Bookings, manage your billing address and edit your password and account details.', 'wp-travel' ); ?></p>
  <div class="lists clearfix">
	<div class="list-item">
	  <div class="list-item-wrapper">
		<div class="item">
		  <a href="#" class="dashtab-nav" data-tabtitle="bookings"><strong><?php esc_html_e( 'My Bookings', 'wp-travel' ); ?></strong></a>
		  <div class="box-content">
			<?php if ( ! empty( $bookings_glance ) && is_array( $bookings_glance ) ) : ?>
			<ul>
				<?php
				foreach ( $bookings_glance as $key => $bk_id ) :

					$trip_id = get_post_meta( $bk_id, 'wp_travel_post_id', true );

					if ( ! $trip_id ) {
						continue;
					}
					?>
				<li>
				  <a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>"><?php echo esc_html( get_the_title( $trip_id ) ); ?></a>
				</li>

					<?php
			  endforeach;
				?>
			</ul>
			<a href="#" data-tabtitle="bookings" class="dashtab-nav"><strong><?php esc_html_e( 'View All', 'wp-travel' ); ?></strong></a>
			<?php else : ?>
			  <p>
				<?php esc_html_e( 'You haven&lsquo;t booked any trips yet.', 'wp-travel' ); ?>
			  </p>
			  <a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book some trips now', 'wp-travel' ); ?></a>
			<?php endif; ?>
		  </div>
		  <div class="box-actions">
		  </div>
		</div>
	  </div>
	</div>
	<div class="list-item">
	  <div class="list-item-wrapper">
		<div class="item">
		  <a href="#" class="dashtab-nav" data-tabtitle="address"><strong><?php esc_html_e( 'Address', 'wp-travel' ); ?></strong></a>
		  <div class="box-content">
			<?php if ( is_array( $biling_glance_data ) && ! empty( $biling_glance_data ) ) : ?>
			  <p>
				<?php echo esc_html( $biling_glance_data['billing_address'] ); ?><br>
				<?php echo esc_html( $biling_glance_data['billing_city'] ); ?><br>
				<?php echo esc_html( $biling_glance_data['billing_zip_code'] ); ?><br>
				<?php echo esc_html( $biling_glance_data['billing_country'] ); ?><br>
			  </p>
			<?php endif; ?>
		  </div>
		  <div class="box-actions">
			<a href="#" data-tabtitle="address" class="action dashtab-nav edit">
			  <i class="wt-icon wt-icon-pencil-alt" aria-hidden="true"></i>
			  <span><?php esc_html_e( 'Edit', 'wp-travel' ); ?></span>
			</a>
		  </div>
		</div>
	  </div>
	</div>
	<div class="list-item">
	  <div class="list-item-wrapper">
		<div class="item">
		  <a href="#" class="dashtab-nav" data-tabtitle="account"><strong><?php esc_html_e( 'Account Info', 'wp-travel' ); ?></strong></a>
		  <div class="box-content">
			<p>
				<?php echo esc_html( $current_user->first_name . ' ' . $current_user->last_name ); ?><br>
				<?php echo esc_html( $current_user->user_email ); ?><br>
			</p>
		  </div>
		  <div class="box-actions">
			<a data-tabtitle="account" class="action edit dashtab-nav" href="#">
			  <i class="wt-icon wt-icon-pencil-alt" aria-hidden="true"></i>
			  <span><?php esc_html_e( 'Edit', 'wp-travel' ); ?></span>
			</a>
			<a href="#" data-tabtitle="account" class="action dashtab-nav action change-password">
				<?php esc_html_e( 'Change Password', 'wp-travel' ); ?></a>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>
