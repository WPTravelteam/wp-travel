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
		$this->field['rows'] = isset( $this->field['rows'] ) ? $this->field['rows'] : '';
		$this->field['cols'] = isset( $this->field['cols'] ) ? $this->field['cols'] : '';

		$output = sprintf( '<textarea id="%s" name="%s" placeholder="%s" rows="%d" cols="%d" %s>', $this->field['id'], $this->field['name'], $this->field['placeholder'], $this->field['rows'], $this->field['cols'], $validations );
		$output .= $this->field['default'];
		$output .= sprintf( '</textarea>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
