<?php
/**
 * Base class for WP Travel All tables.
 *
 * @package WP_Travel
 * @subpackage lib
 * @since 4.4.7
 */

if ( ! class_exists( 'WP_Travel_Tables_Base' ) ) {
	/**
	 * Base Class
	 *
	 * @since 4.4.7
	 */
	abstract class WP_Travel_Tables_Base {

		/**
		 * Table Name.
		 *
		 * @since 4.4.7
		 * @var string
		 */
		protected $table_name = '';

	}
}
