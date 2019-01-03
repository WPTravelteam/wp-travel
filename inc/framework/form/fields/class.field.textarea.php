<?php
class WP_Travel_FW_Field_Textarea {
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
				$attributes .= sprintf( ' %s="%s" ', $attribute, $attribute_val );
			}
		}

		$output = sprintf( '<textarea id="%s" name="%s" %s %s>', $this->field['id'], $this->field['name'], $validations, $attributes );
		$output .= $this->field['default'];
		$output .= sprintf( '</textarea>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
