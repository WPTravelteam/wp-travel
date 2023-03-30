<?php
class WP_Travel_FW_Field_Heading {
	protected $field;
	protected $field_type = 'heading';
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true ) {
		$attribute	  = isset( $this->field['attributes'] ) ? $this->field['attributes'] : array();
		$tag          = isset( $attribute['heading_tag'] ) ? esc_attr( $attribute['heading_tag'] ) : 'h1';
		$before_field = isset( $this->field['before_field'] ) ? $this->field['before_field'] : '';
		$after_field  = isset( $this->field['after_field'] ) ? $this->field['after_field'] : '';
		$class        = empty( $this->field['class'] ) ? $this->field['wrapper_class'] : $this->field['class'];
		$output       = sprintf( '%s<%s id="%s" class="%s">%s</%s>%s', $before_field, $tag, $this->field['id'], $class, $this->field['label'], $tag, $after_field );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
