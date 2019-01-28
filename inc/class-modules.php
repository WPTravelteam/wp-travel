<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WP_Travel_Modules' ) ) :
  /**
   * WP Travel Modules
   */
  class WP_Travel_Modules {

    private static $modules;

    public static function init() {
      self::$modules = array(
        'wp-travel-field-editor' => array(
          'title' => __( 'WP Travel Field Editor' ),
          'core_class' => 'WP_Travel_Field_Editor_Core',
        )
      );

      self::includes();
      self::activate();
    }

    public static function includes() {
      foreach ( self::$modules as $key => $module ) {
        $module_core_file = sprintf( '%s/modules/%s/%s-core.php', WP_TRAVEL_ABSPATH, $key, $key );
        if ( file_exists( $module_core_file ) ) {
          include_once( $module_core_file );
        }
      }
    }

    public static function activate() {
      foreach ( self::$modules as $key => $module ) {
        if ( class_exists( $module['core_class'] ) ) {
          $module['core_class']::init();
        }
      }
    }
  }

  WP_Travel_Modules::init();
endif;
