<?php
/**
 * CSV Exporter Class for WP Travel
 * 
 * @package WP_Travel\inc\admin
 */
class WP_Travel_CSV_Exporter {

    public function __construct() {
        // Initialize hooks.
        $this->init_hooks();

    }

    public function init_hooks() {

		add_action( 'export_wp', array( $this, 'generate_csv' ) );
		add_action('admin_notices', array($this, 'add_export_button') );

    }

    function add_export_button(){
        $screen = get_current_screen();
        $csv_supported = array( 'edit-itinerary-booking', 'edit-' .WP_TRAVEL_POST_TYPE, 'edit-tour-extras', 'edit-itinerary-enquiries', 'edit-wp-travel-coupons' );
		if( ! in_array( $screen->id, $csv_supported ) ){
			return;
		} else {
			?>
            <div class="wrap export-to-csv">
                <a href="<?php echo admin_url( 'export.php' );?>" class="page-title-action"><?php esc_html_e( 'Export to CSV', 'wp-travel' ); ?></a>
            </div>

			<div class="wrap import-csv">
                <a href="<?php echo admin_url( 'export.php' );?>" class="page-title-action"><?php esc_html_e( 'Import CSV', 'wp-travel' ); ?></a>
            </div>

            <style scoped>
                .wrap.export-to-csv {
                    float: none;
                    display: inline;
                    position: absolute;
                    left: 14em;
                    top: 1.44em;
                }

				.wrap.import-csv {
                    float: none;
                    display: inline;
                    position: absolute;
                    left: 24em;
                    top: 1.44em;
                }
            </style>
			<?php
		}
    }
    
    /**
	 * Process content of CSV file
	 *
	 * @since 0.1
	 **/
	public function generate_csv( $args ) {

        // Check for current user privileges 
        if( !current_user_can( 'manage_options' ) ){ return false; }
        // Check if we are in WP-Admin
		if( !is_admin() ){ return false; }

        global $wpdb;
        $post_type = $args['content'];
        $query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key) 
        FROM $wpdb->posts 
        LEFT JOIN $wpdb->postmeta 
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
        WHERE $wpdb->posts.post_type = '%s' 
        AND $wpdb->postmeta.meta_key != '' 
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)' 
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
        ";
        $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

        $csv_supported = array( 'itinerary-booking', WP_TRAVEL_POST_TYPE, 'tour-extras', 'wp-travel-payment', 'itinerary-enquiries', 'wp-travel-coupons' );

		if ( in_array( $args['content'], $csv_supported ) ) {

			$defaults = array( 
                'content'    => 'all',
                'author'     => false,
                'category'   => false,
                'start_date' => false,
                'end_date'   => false,
                'status'     => false,
			);

			$user_args = array(
				'fields' => 'all_with_meta',
			);

			$merge_args = array_merge( $defaults, $user_args );

			$args = wp_parse_args( $args, $merge_args );

			$wpt_data_posts = get_posts( array( 'post_type' => $args['content'], 'post_status' => 'publish', 'posts_per_page' => -1 ) );

			if ( ! $wpt_data_posts ) {
				$referer = add_query_arg( 'error', 'empty', wp_get_referer() );
				wp_redirect( $referer );
				exit;
			}

			$sitename = sanitize_key( get_bloginfo( 'name' ) );
			if ( ! empty( $sitename ) ) {
				$sitename .= '.';
			}
			$filename = $sitename . $args['content'] . date( 'Y-m-d-H-i-s' ) . '.csv';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );

			$exclude_data = apply_filters( 'wp_travel_booking_data_exclude_data', array() );

			global $wpdb;

			$fields  = $meta_keys;

			$headers = array();

			foreach ( $fields as $key => $field ) {
				if ( in_array( $field, $exclude_data ) ) {
					unset( $fields[ $key ] );
				} else {
					$headers[] = '"' . strtolower( $field ) . '"';
				}
			}

			echo implode( ',', $headers ) . "\n";

			foreach ( $wpt_data_posts as $wpt_post ) {
				$data = array();
				foreach ( $fields as $field ) {
					$value  = isset( $wpt_post->{$field} ) ? $wpt_post->{$field} : '';
					$value  = is_array( $value ) ? serialize( $value ) : $value;
					$data[] = '"' . str_replace( '"', '""', $value ) . '"';
				}

				echo implode( ',', $data ) . "\n";
			}

			exit;
		}
	}

}

new WP_Travel_CSV_Exporter();
