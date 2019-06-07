<?php
class WP_Travel_FW_Field_Date extends WP_Travel_FW_Field_Text {
	protected $field;
	protected $field_type = 'text';
	function init( $field ) {
		$this->field = $field;
		$this->field['attributes']['autocomplete'] = 'off';
		return $this;
	}

	function render( $display = true ) {
		$js_date_format = wp_travel_date_format_php_to_js();
		$output = parent::render( false );

		$lang_code = explode( '-', get_bloginfo( 'language' ) );
		$locale    = $lang_code[0];

		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );

		$wp_content_file_url = WP_CONTENT_URL . '/languages/wp-travel/datepicker/';
		$default_url         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) );

		$filename = 'datepicker.' . $locale . '.js';

		if (
			! file_exists( trailingslashit( $wp_content_file_path ) . $filename )
			&& file_exists( trailingslashit( $default_path ) . $filename )
		) {
			$locale = 'en';
		}

		$max_today = isset( $this->field['attributes'] ) && isset( $this->field['attributes']['data-max-today'] ) ? $this->field['attributes']['data-max-today'] : '';
		$output   .= '<script>';
		$output   .= 'jQuery(document).ready( function($){ ';
		$output   .= '$("#' . $this->field['id'] . '").wpt_datepicker({
							language: "' . $locale . '",';
		$output   .= "dateFormat: '" . $js_date_format . "',";
		if ( '' !== $max_today && true == $max_today ) {
			$output .= 'maxDate: new Date(),';
		} else if( '' !== $max_today && false == $max_today ) {
			$output .= 'minDate: new Date(),';
		}

		$output .= '});';
		$output .= '} );';

		// $output .= 'window.ParsleyValidator.addValidator("dateformat", function (value, requirement) {';
		// $output .= 'return moment( value, requirement,true).isValid(); ';
		// $output .= '       ';
		// $output .= '}, 32)';
		$output .= '</script>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}

	function render_old( $display = true ) {
		$js_date_format = wp_travel_date_format_php_to_js();
		$validations = '';
		if ( isset( $this->field['validations'] ) ) {
			foreach ( $this->field['validations'] as $key => $attr ) {
				$validations .= sprintf( 'data-parsley-%s="%s"', $key, $attr );
			}
		}
		$attributes = '';
		if ( isset( $this->field['attributes'] ) ) {
			foreach ( $this->field['attributes'] as $attribute => $attribute_val ) {
				$attributes .= sprintf( '%s="%s"', $attribute, $attribute_val );
			}
		}
		$output = sprintf( '<input data-date-format="%s" type="%s" id="%s" name="%s" value="%s" %s class="%s" %s >', $js_date_format, $this->field_type, $this->field['id'], $this->field['name'], $this->field['default'], $validations, $this->field['class'], $attributes );

		$lang_code = explode( '-', get_bloginfo( 'language' ) );
		$locale    = $lang_code[0];

		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );

		$wp_content_file_url = WP_CONTENT_URL . '/languages/wp-travel/datepicker/';
		$default_url         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) );

		$filename = 'datepicker.' . $locale . '.js';

		if (
			! file_exists( trailingslashit( $wp_content_file_path ) . $filename )
			&& file_exists( trailingslashit( $default_path ) . $filename )
		) {
			$locale = 'en';
		}

		$max_today = isset( $this->field['attributes'] ) && isset( $this->field['attributes']['data-max-today'] ) ? true : false;
		$output   .= '<script>';
		$output   .= 'jQuery(document).ready( function($){ ';
		$output   .= '$("#' . $this->field['id'] . '").wpt_datepicker({
							language: "' . $locale . '",';
		if ( $max_today ) {
			$output .= 'maxDate: new Date()';
		} else {
			$output .= 'minDate: new Date()';
		}

		$output .= '});';
		$output .= '} )';
		$output .= '</script>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
