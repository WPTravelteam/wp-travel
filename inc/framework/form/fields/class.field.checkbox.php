<?php
/**
 * Input field class for checkbox.
 *
 * @since 1.0.5
 * @package WP_Travel
 */

class WP_Travel_FW_Field_Checkbox {
	private $field;
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true ) {
		$validations = '';
		if ( isset( $this->field['validations'] ) ) {
			foreach ( $this->field['validations'] as $key => $attr ) {
				if ( 'maxlength' === $key ) { // issue fixes for field editor conflict with validaton. checkbox doesn't have maxlength.
					continue;
				}
				$validations .= sprintf( 'data-parsley-%s="%s"', $key, $attr );
			}
		}
		$output = '';
		// $output = sprintf( '<select id="%s" name="%s" %s>', $this->field['id'], $this->field['name'], $validations );
		if ( ! empty( $this->field['options'] ) ) {
			$index = 0;
			// Custom Fields. [travelers fields have _default ]
			if ( ! isset( $this->field['_default'] ) || ( isset( $this->field['_default'] ) && ! $this->field['_default'] ) && count( $this->field['options'] ) > 0 ) {
				$ignore_mapping_fields = array( 'terms_and_conditions[]' );
				if ( ! in_array( $this->field['name'], $ignore_mapping_fields ) ) {
					$mapped_options = array();
					foreach( $this->field['options'] as $option ) {
						$mapped_options[ $option ] = $option;
					}
					$this->field['options'] = $mapped_options;
				}
			}
			foreach ( $this->field['options'] as $key => $value ) {

				// Option Attributes.
				$option_attributes = '';
				if ( isset( $this->field['option_attributes'] ) && count( $this->field['option_attributes'] ) > 0 ) {

					foreach ( $this->field['option_attributes'] as $key1 => $attr ) {
						if ( ! is_array( $attr ) ) {
							$option_attributes .= sprintf( '%s="%s"', $key1, $attr );
						} else {
							foreach ( $attr as $att ) {
								$option_attributes .= sprintf( '%s="%s"', $key1, $att );
							}
						}
					}
				}
				if ( is_array( $this->field['default'] ) && count( $this->field['default'] ) > 0 ) {

						$checked = ( in_array( $key, $this->field['default'] ) ) ? 'checked' : '';

				} else {
					$checked = ( $key === $this->field['default'] ) ? 'checked' : '';
				}

				$checkbox_value          = is_numeric( $key ) ? $value : $key;
				$checkbox_value          = wp_strip_all_tags( $checkbox_value );
				$error_coontainer_id     = sprintf( 'error_container-%s', $this->field['id'] );
				$parsley_error_container = ( 0 === $index ) ? sprintf( 'data-parsley-errors-container="#%s"', $error_coontainer_id ) : '';
				$output                 .= sprintf( '<label class="radio-checkbox-label"><input type="checkbox" name="%s[]" %s value="%s" %s %s %s/>%s</label>', $this->field['name'], $option_attributes, $checkbox_value, $checked, $validations, $parsley_error_container, $value );
				$index++;
			}
			$output .= sprintf( '<div id="%s"></div>', $error_coontainer_id );
		}
		// $output .= sprintf( '</select>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
