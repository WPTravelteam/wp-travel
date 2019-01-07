<?php
class WP_Travel_FW_Field_Text {
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

		$output = sprintf( '%s<input type="%s" id="%s" name="%s" value="%s" %s class="%s" %s>', $before_field, $this->field_type, $this->field['id'], $this->field['name'], $this->field['default'], $validations, $this->field['class'], $attributes );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
