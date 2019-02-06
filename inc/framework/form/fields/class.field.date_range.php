<?php
class WP_Travel_FW_Field_Date_Range {
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
			$before_field       = sprintf( '<span class="wp-travel-field-before %s">%s</span>', $before_field_class, $this->field['before_field'] );
		}

		$output = '';

		$defaults = $this->field['default'];
		foreach ( $defaults as $default ) {
			$js_date_format = wp_travel_date_format_php_to_js();
			// $input_class = isset( $default['class'] ) ? $default['class'] : '';
			$name  = isset( $default['name'] ) ? $default['name'] : '';
			$label = isset( $default['label'] ) ? $default['label'] : '';
			$value = isset( $default['value'] ) ? $default['value'] : '';
			$id    = isset( $default['id'] ) ? $default['id'] : '';

			$output .= '<span class="trip-duration-calender">';
			$output .= sprintf( '<small>%s</small>', $label );
			$output .= sprintf( '<input data-date-format="%s" value="%s" class="%s" type="text" id="%s" name="%s">', $js_date_format, $value, $this->field['class'], $id, $name );
			$output .= sprintf( '<label for="%s">', $id );
			$output .= '<span class="calender-icon"></span>';
			$output .= '</label>';
			$output .= '</span>';
		}

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
