<?php
class WP_Travel_FW_Field_Range {
	protected $field;
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true ) {
		$validations = '';
		if ( isset( $this->field['validations'] ) ) {
			foreach ( $this->field['validations'] as $key => $attr ) {
				$validations .= sprintf( ' %s="%s" data-parsley-%s="%s"', $key, $attr, $key, $attr );
			}
		}
		$attributes = '';
		if ( isset( $this->field['attributes'] ) ) {
			foreach ( $this->field['attributes'] as $attribute => $attribute_val ) {
				$attributes .= sprintf( ' %s="%s" ', $attribute, $attribute_val );
			}
		}

		$before_field = '';
		if ( isset( $this->field['before_field'] ) ) {
			$before_field_class = isset( $this->field['before_field_class'] ) ? $this->field['before_field_class'] : '';
			$before_field = sprintf( '<span class="wp-travel-field-before %s">%s</span>', $before_field_class, $this->field['before_field'] );
		}

		
		$output = sprintf( '<input type="text" id="%s" class="price-amount" readonly style="border:0; color:#f6931f; font-weight:bold;">', $this->field['id'] );
		
		$defaults = $this->field['default'];
		foreach ( $defaults as $default ) {
			$input_class = isset( $default['class'] ) ? $default['class'] : '';
			$name = isset( $default['name'] ) ? $default['name'] : '';
			$value = isset( $default['value'] ) ? $default['value'] : '';
			$output .= sprintf( '<input type="hidden" class="%s %s " name="%s" value="%s">', $this->field['class'], $input_class, $name, $value );
		}
		$output .= '<div class="wp-travel-range-slider"></div>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
