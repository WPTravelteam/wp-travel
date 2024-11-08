<?php

/*
 * @author Wp-Travel
 * @since 6.8.0
 * Settings Import/Export
 */

class WP_Travel_Import_Export {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'wp_travel_import_export_api' ) );
    }

    // Registering the REST API routes
    public function wp_travel_import_export_api() {
        register_rest_route(
            'wp-travel/v1',
            '/import-settings-data',
            array(
                'methods'             => 'POST',
                'permission_callback' => array( $this, 'check_permissions' ),
                'callback'            => array( $this, 'wp_travel_import_data' ),
            )
        );

        register_rest_route(
            'wp-travel/v1',
            '/export-settings-data',
            array(
                'methods'             => 'GET',
                'permission_callback' => array( $this, 'check_permissions' ),
                'callback'            => array( $this, 'wp_travel_export_data' ),
            )
        );
    }

    // Check if the current user has permission
    public function check_permissions() {
        return current_user_can( 'manage_options' );
    }

    // Function to handle importing settings
    public function wp_travel_import_data( WP_REST_Request $request ) {
        // Get the raw JSON data from the request
        $settings_data = $request->get_body_params();
        
        if ( empty( $settings_data['settings_data'] ) ) {
            return new WP_REST_Response( 'No settings data provided.', 400 );
        }

        // Decode the JSON data into a PHP array
        $settings_data_array = json_decode( $settings_data['settings_data'], true );

        // Check if JSON decoding was successful
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_REST_Response( 'Invalid JSON data provided.', 400 );
        }

        // Update the option in the database
        if ( update_option( 'wp_travel_settings', $settings_data_array ) ) {
            return new WP_REST_Response( 'Settings imported successfully.', 200 );
        } else {
            return new WP_REST_Response( 'Failed to import settings.', 500 );
        }
    }

    // Function to handle exporting settings
    public function wp_travel_export_data() {
        // Retrieve WP Travel settings
        $settings = get_option( 'wp_travel_settings' );

        if ( empty( $settings ) ) {
            return new WP_REST_Response( 'No settings data found.', 404 );
        }

        // Create JSON response with settings data
        $response = new WP_REST_Response( $settings, 200 );
        $response->header( 'Content-Type', 'application/json' );
        $response->header( 'Content-Disposition', 'attachment; filename=wp-travel-settings.json' );

        return $response;
    }
}

new WP_Travel_Import_Export();