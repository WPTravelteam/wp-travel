<?php
class WP_Travel_FW_Field_Text_Info {
	protected $field;
	protected $field_type = 'text_info';
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true ) {
		$before_field = isset( $this->field['before_field'] ) ? $this->field['before_field'] : '';
		$output = sprintf( '<div class="wp-travel-text-info"><span class="wp-travel-currency-symbol">%s</span> <span class="wp-travel-info-content" id="%s">%s</span></div>', $before_field, $this->field['id'], $this->field['default'] );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
