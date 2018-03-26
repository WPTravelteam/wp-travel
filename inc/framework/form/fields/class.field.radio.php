<?php
/**
 * Input field class for radio.
 *
 * @since 1.0.5
 * @package WP-Travel/inc/framework/form/fields/
 */

class WP_Travel_FW_Field_Radio {
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

				$checked = ( $key == $this->field['default'] ) ? 'checked' : '';
				$output .= sprintf( '<label class="radio-checkbox-label"><input type="radio" name="%s" %s value="%s" %s />%s</label>', $this->field['name'],  $option_attributes, $key, $checked, $value );
			}
		}
		// $output .= sprintf( '</select>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
