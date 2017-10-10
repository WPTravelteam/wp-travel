<?php
class WP_Travel_FW_Field_Select {
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
		$output = sprintf( '<select id="%s" name="%s" %s>', $this->field['id'], $this->field['name'], $validations );
		if ( ! empty( $this->field['options'] ) ) {
			foreach ( $this->field['options'] as $key => $value ) {
				$selected = ( $key == $this->field['default'] ) ? 'selected' : '';
				$output .= sprintf( '<option value="%s" %s>%s</option>', $key, $selected, $value );
			}
		}
		$output .= sprintf( '</select>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
