<?php
/**
 * Input field class for checkbox.
 *
 * @since 1.0.5
 * @package WP-Travel/inc/framework/form/fields/
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
				$validations .= sprintf( 'data-parsley-%s="%s"', $key, $attr );
			}
		}
		$output = '';
		// $output = sprintf( '<select id="%s" name="%s" %s>', $this->field['id'], $this->field['name'], $validations );
		if ( ! empty( $this->field['options'] ) ) {
			$index = 0;
			foreach ( $this->field['options'] as $key => $value ) {

				// Option Attributes.
				$option_attributes = '';
				if ( isset( $this->field['option_attributes'] ) && count( $this->field['option_attributes'] ) > 0 ) {

					foreach ( $this->field['option_attributes'] as $key1 => $attr ) {
						if ( ! is_array( $attr ) ) {
							$option_attributes .= sprintf( '%s="%s"', $key1, $attr );
						} else {
							foreach( $attr as $att ) {
								$option_attributes .= sprintf( '%s="%s"', $key1, $att );
							}
						}
					}
				}
				if ( is_array( $this->field['default'] ) && count( $this->field['default'] ) > 0 ) {

						$checked = ( in_array( $key, $this->field['default'] ) ) ? 'checked' : '';

				} else {
					$checked = ( $key == $this->field['default'] ) ? 'checked' : '';
				}

				$error_coontainer_id = sprintf( 'error_container-%s', $this->field['id'] );
				$parsley_error_container = ( 0 === $index ) ? sprintf( 'data-parsley-errors-container="#%s"', $error_coontainer_id ) : '';
				$output .= sprintf( '<label class="radio-checkbox-label"><input type="checkbox" name="%s[]" %s value="%s" %s %s %s/>%s</label>', $this->field['name'],  $option_attributes, $key, $checked, $validations,$parsley_error_container, $value );
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
