<?php
class WP_Travel_FW_Field_Date {
	protected $field;
	protected $field_type = 'text';
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true ) {
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
		$output = sprintf( '<input type="%s" id="%s" name="%s" value="%s" %s class="%s" %s >', $this->field_type, $this->field['id'], $this->field['name'], $this->field['default'], $validations, $this->field['class'], $attributes );

		$lang_code = explode( '-', get_bloginfo('language') );
		$locale = $lang_code[0];

		$output .= '<script>';
		$output .= 'jQuery(document).ready( function($){ ';
		$output .= 		'$("#' . $this->field['id'] . '").datepicker({
							language: "' . $locale . '",		
							minDate: new Date()
						});';
		$output .= '} )';
		$output .= '</script>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
