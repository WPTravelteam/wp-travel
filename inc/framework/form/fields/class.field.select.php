<?php
class WP_Travel_FW_Field_Select {
	protected $field;
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
				if ( 'placeholder' !== $attribute ) {
					$attributes .= sprintf( ' %s="%s" ', $attribute, $attribute_val );
				}
			}
		}

		$output = sprintf( '<select id="%s" name="%s" class="%s" %s %s>', $this->field['id'], $this->field['name'], $this->field['class'], $validations, $attributes );
		if ( ! empty( $this->field['attributes']['placeholder'] ) ) {
			$this->field['options'] = wp_parse_args( $this->field['options'], array(
				'' => $this->field['attributes']['placeholder'],
			));
		}

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

				$selected = ( $key == $this->field['default'] ) ? 'selected' : '';
				$output .= sprintf( '<option %s value="%s" %s>%s</option>', $option_attributes, $key, $selected, $value );
			}
		}
		$output .= sprintf( '</select>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
