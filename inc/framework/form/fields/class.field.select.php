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
			$this->field['options'] = wp_parse_args(
				$this->field['options'],
				array(
					'' => $this->field['attributes']['placeholder'],
				)
			);
		}
		
		// Custom Fields. [travelers fields have _default ]
		if ( ! isset( $this->field['_default'] ) || ( isset( $this->field['_default'] ) && ! $this->field['_default'] ) && count( $this->field['options'] ) > 0 ) {
			$ignore_mapping_fields = array( 'wp_travel_country', 'wp_travel_booking_option', 'wp_travel_payment_mode', 'wp_travel_enquiry_post_id', 'wp_travel_post_id' ); // Temp fixes. Neeed argument in field args like use_label_as_value
			// Note : select option need to be key = value for field editor so need to map option except above options.
			if ( ! in_array( $this->field['name'], $ignore_mapping_fields ) ) {
				$mapped_options = array();
				foreach( $this->field['options'] as $option) {
					$mapped_options[ $option ] = $option;
				}
				$this->field['options'] = $mapped_options;
			}
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
							foreach ( $attr as $att ) {
								$option_attributes .= sprintf( '%s="%s"', $key1, $att );
							}
						}
					}
				}

				$selected = ( $key == $this->field['default'] ) ? 'selected' : '';
				$output  .= sprintf( '<option %s value="%s" %s>%s</option>', $option_attributes, $key, $selected, $value );
			}
		}
		$output .= sprintf( '</select>' );

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
