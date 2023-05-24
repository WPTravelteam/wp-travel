<?php
/**
 * Input field class for radio.
 *
 * @since 1.0.5
 * @package WP_Travel
 */

class WP_Travel_FW_Field_Radio {
	private $field;
	function init( $field ) {
		$this->field = $field;
		return $this;
	}

	function render( $display = true, $trip_id= "" ) {
		$validations = '';
		if ( isset( $this->field['validations'] ) ) {
			foreach ( $this->field['validations'] as $key => $attr ) {
				$validations .= sprintf( 'data-parsley-%s="%s"', $key, $attr );
				$validations .= sprintf( '%s="%s"', $key, $attr );
			}
		}
		$output = '';
		if ( ! empty( $this->field['options'] ) ) {
			$index = 0;
			// Custom Fields. [travelers fields have _default ]
			if ( ! isset( $this->field['_default'] ) || ( isset( $this->field['_default'] ) && ! $this->field['_default'] ) && count( $this->field['options'] ) > 0 ) {
				$ignore_mapping_fields = array( 'wp_travel_payment_gateway' );
				if ( ! in_array( $this->field['name'], $ignore_mapping_fields ) ) {
					$mapped_options = array();
					foreach( $this->field['options'] as $option ) {
						$mapped_options[ $option ] = $option;
					}
					$this->field['options'] = $mapped_options;
				}
			}

			$payment_gateways = $this->field['options'];
			$payment = $payment_gateways;
			$by_billing_address = '';
			if ( class_exists('WP_Travel_Pro') && isset( wptravel_get_settings()['enable_conditional_payment'] ) && wptravel_get_settings()['enable_conditional_payment'] == 'yes' ){

				if ( isset( wptravel_get_settings()['enable_CP_by_billing_address'] ) && wptravel_get_settings()['enable_CP_by_billing_address'] == 'yes' ){
					return;
				}
				
				$trip_location = wp_get_post_terms( $trip_id[array_key_first($trip_id)]['trip_id'], 'travel_locations', array( 'fields' => 'all' ) )[0]->slug;
				
				add_action('wp_enqueue_scripts', function(){
					wp_localize_script( 'wp-travel-script', '_wp_travel_conditional_payment_list', wptravel_get_settings()['conditional_payment_list'] );
				});

				$conditional_payment = array();
				foreach( wptravel_get_settings()['conditional_payment_list'] as $value ){

					if( array_key_exists( $trip_location, $conditional_payment ) ){
						array_push( $conditional_payment[$trip_location], $value['payment_gateway'] );
					}else{
						$conditional_payment[$value['trip_location']] = array( $value['payment_gateway'] );
					}					
					$by_billing_address = isset( $value['enable_CP_by_billing_address'] ) ?$value['enable_CP_by_billing_address'] : '';
				}

				if( array_key_exists( $trip_location, $conditional_payment )  ){
					$payment_list = array();
	
					$conditional_payment = $conditional_payment[ $trip_location ];
	
					foreach( $conditional_payment as $value ){
						$payment_list[$value] = isset( $payment_gateways[$value] ) ? $payment_gateways[$value] : '';
					}
	
					$payment = $payment_list;
	
				}else{
					$payment = $payment_gateways;
				}
			}

			
			foreach ( $payment as $key => $value ) {

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

				$checked                 = ( $key == $this->field['default'] ) ? 'checked' : '';
				$error_coontainer_id     = sprintf( 'error_container-%s', $this->field['id'] );
				$parsley_error_container = ( 0 === $index ) ? sprintf( 'data-parsley-errors-container="#%s"', $error_coontainer_id ) : '';
				$output                 .= sprintf( '<div class="wp-travel-radio"><input type="radio" id="wp-travel-payment-%s" name="%s" %s value="%s" %s %s %s/><label for="wp-travel-payment-%s" class="radio-checkbox-label">%s</label></div>', $key, $this->field['name'], $option_attributes, $key, $checked, $validations, $parsley_error_container, $key, $value );
				$index++;
			}
			$output .= sprintf( '<div id="%s"></div>', $error_coontainer_id );
		}
		// $output .= sprintf( '</select>' );

		
		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
