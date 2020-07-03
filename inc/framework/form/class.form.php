<?php
/**
 * Form Class.
 *
 * @package inc/framework/form/
 */

include WP_TRAVEL_ABSPATH . 'inc/framework/form/class.field.php';

/**
 * WP Travel Form.
 * @since 1.0.0
 */
class WP_Travel_FW_Form {
	/**
	 * Form Option.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $form_options;
	/**
	 * Form Fields
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private $fields;
	/**
	 * Init Function.
	 *
	 * @param array $form_options Attributes of form.
	 * @since 1.0.0
	 * @return Obj
	 */
	function init( $form_options = array() ) {
		$this->hooks();
		$this->form_options['id'] = isset( $form_options['id'] ) ? $form_options['id'] : '' ;
		$this->form_options['class'] = isset( $form_options['class'] ) ? $form_options['class'] : $form_options['id'] ;
		$this->form_options['wrapper_class'] = isset( $form_options['wrapper_class'] ) ? $form_options['wrapper_class'] : $form_options['id'] . '-wrapper' ;
		$this->form_options['hook_prefix'] = isset( $form_options['hook_prefix'] ) ? $form_options['hook_prefix'] : $this->slugify( $form_options['id'], array(), '_' );

		$this->form_options['submit_button']['id'] = isset( $form_options['submit_button']['id'] ) ? $form_options['submit_button']['id'] : '';
		$this->form_options['submit_button']['name'] = isset( $form_options['submit_button']['name'] ) ? $form_options['submit_button']['name'] : '';
		$this->form_options['submit_button']['value'] = isset( $form_options['submit_button']['value'] ) ? $form_options['submit_button']['value'] : '';
		$this->form_options['submit_button']['class'] = isset( $form_options['submit_button']['class'] ) ? $form_options['submit_button']['class'] : '';

		$this->form_options['nonce']['field'] = isset( $form_options['nonce']['field'] ) ? $form_options['nonce']['field'] : '';
		$this->form_options['nonce']['action'] = isset( $form_options['nonce']['action'] ) ? $form_options['nonce']['action'] : '';

		$this->form_options['multipart'] = isset( $form_options['multipart'] ) && $form_options['multipart'] ? true : false;
		return $this;
	}

	/**
	 * Hooks to run with form.
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Init assets.
	 */
	public function init_assets() {
		wp_enqueue_script( 'jquery-parsley' );
	}

	/**
	 * Array list of form fields.
	 *
	 * @param array $fields form fields.
	 * @since 1.0.0
	 * @return obj
	 */
	function fields( $fields ) {
		$this->fields = $fields;
		$priority = array();
		foreach ( $fields as $key => $row ) {
		    $priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
		}
		array_multisort( $priority, SORT_ASC, $this->fields );
		return $this;
	}

	/**
	 * Template
	 *
	 * @return void
	 */
	function template() {
		$this->init_assets();
		?>
			<div class="<?php echo esc_attr( $this->form_options['wrapper_class'] ); ?>">
				<form action="" method="post" id="<?php echo esc_attr( $this->form_options['id'] ); ?>"  class="<?php echo esc_attr( $this->form_options['class'] ); ?>" enctype="multipart/form-data" >
					<?php do_action( $this->form_options['hook_prefix'] . '_before_form_field' ); ?>
					<?php
					$fields = new WP_Travel_FW_Field();
					$fields->init( $this->fields )->render();
					?>
					<div class="wp-travel-form-field button-field">
						<?php do_action( $this->form_options['hook_prefix'] . '_before_submit_button' ); ?>
						<?php wp_nonce_field( $this->form_options['nonce']['action'], $this->form_options['nonce']['field'] ); ?>
						<?php
						printf( '<input type="submit" name="%s" id="%s" value="%s" class="%s">', esc_attr( $this->form_options['submit_button']['name'] ), esc_attr( $this->form_options['submit_button']['id'] ), esc_attr( $this->form_options['submit_button']['value'] ), esc_attr( $this->form_options['submit_button']['class'] ) );
						?>
						<?php do_action( $this->form_options['hook_prefix'] . '_after_submit_button' ); ?>
					</div>
					<?php do_action( $this->form_options['hook_prefix'] . '_after_form_field' ); ?>
				</form>
			</div>

		<?php
		$this->init_validation( $this->form_options['id'] );
	}

	/**
	 * Register form assets
	 */
	public function register_assets() {
		wp_register_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ) );

	}

	public function init_validation( $id ) {
		if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
			$this->init_assets();
		}
		?>
		<script> jQuery( function( $ ) { if (typeof parsley == "object") { $('#<?php echo esc_attr( $id ); ?>').parsley(); } } ); </script>
		<?php
	}

	/**
	 * Template [unused]
	 *
	 * @return void
	 */
	function template_new() {
		wp_enqueue_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ) );
		$multipart = '';
		if ( $this->form_options['multipart']  ) {
			$multipart = 'enctype="multipart/form-data"';
		}
		?>
			<div class="<?php echo esc_attr( $this->form_options['wrapper_class'] ); ?>">
				<form action="" method="post" id="<?php echo esc_attr( $this->form_options['id'] ); ?>"  class="<?php echo esc_attr( $this->form_options['class'] ); ?>" <?php echo esc_html( $multipart ); ?>>
					<?php do_action( $this->form_options['hook_prefix'] . '_before_form_field' ); ?>
					<?php
					$fields = new WP_Travel_FW_Field();
					$fields->init( $this->fields )->render();
					?>
					<div class="wp-travel-form-field button-field">
						<?php do_action( $this->form_options['hook_prefix'] . '_before_submit_button' ); ?>
						<?php wp_nonce_field( $this->form_options['nonce']['action'], $this->form_options['nonce']['field'] ); ?>
						<?php
						printf( '<input type="submit" name="%s" id="%s" value="%s" class="%s">', esc_attr( $this->form_options['submit_button']['name'] ), esc_attr( $this->form_options['submit_button']['id'] ), esc_attr( $this->form_options['submit_button']['value'] ), esc_attr( $this->form_options['submit_button']['class'] ) );
						?>
						<?php do_action( $this->form_options['hook_prefix'] . '_after_submit_button' ); ?>
					</div>
					<?php do_action( $this->form_options['hook_prefix'] . '_after_form_field' ); ?>
				</form>
			</div>
			<script>
			jQuery( function( $ ) {
				if (typeof parsley == "object") {
					$('#<?php echo esc_attr( $this->form_options['id'] ); ?>').parsley();
				}

			} );
			</script>
		<?php
	}

	/**
	 * Slugify.
	 *
	 * @param string $string String.
	 * @param array  $replace Replace String.
	 * @param string $delimiter Delimiter.
	 * @return String.
	 */
	private function slugify( $string, $replace = array(), $delimiter = '-' ) {
		// https://github.com/phalcon/incubator/blob/master/Library/Phalcon/Utils/Slug.php.
		// Save the old locale and set the new locale to UTF-8.
		$old_locale = setlocale( LC_ALL, '0' );
		setlocale( LC_ALL, 'en_US.UTF-8' );
		if ( extension_loaded( 'iconv' ) ) {
			// throw new Exception( 'iconv module not loaded' );
			$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $string );
		} else {
			$clean = sanitize_title( $string );
			$clean = str_replace( '-', '_', $clean );
		}

		if ( ! empty( $replace ) ) {
			$clean = str_replace( ( array ) $replace, ' ', $clean );
		}
		$clean = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
		$clean = strtolower( $clean );
		$clean = preg_replace( "/[\/_|+ -]+/", $delimiter, $clean );
		$clean = trim( $clean, $delimiter );
		// Revert back to the old locale.
		setlocale( LC_ALL, $old_locale );
		return $clean;
	}
}
