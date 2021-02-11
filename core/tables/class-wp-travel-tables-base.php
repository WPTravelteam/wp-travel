<?php
/**
 * Base class for WP Travel All tables.
 *
 * @package WP Travel Core
 * @subpackage lib
 * @since WP Travel 4.4.7
 */

if ( ! class_exists( 'WP_Travel_Tables_Base' ) ) {
	/**
	 * Base Class
	 *
	 * @since WP Travel 4.4.7
	 */
	abstract class WP_Travel_Tables_Base {

		/**
		 * Table Name.
		 *
		 * @since WP Travel 4.4.7
		 * @var string
		 */
		protected $table_name = '';

	}
}
