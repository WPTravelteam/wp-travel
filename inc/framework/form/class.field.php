<?php
class WP_Travel_FW_Field {
	private $fields;
	private $field_types;
	function init( $fields = array() ) {
		$this->includes();
		$this->fields = $fields;
		$this->field_types = $this->field_types();
		return $this;
	}

	private function includes() {
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.text.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.email.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.number.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.hidden.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.select.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.textarea.php';
		include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/fields/class.field.date.php';
	}

	private function field_types() {
		$field_types['text'] = 'WP_Travel_FW_Field_Text';
		$field_types['email'] = 'WP_Travel_FW_Field_Email';
		$field_types['number'] = 'WP_Travel_FW_Field_Number';
		$field_types['hidden'] = 'WP_Travel_FW_Field_Hidden';
		$field_types['select'] = 'WP_Travel_FW_Field_Select';
		$field_types['textarea'] = 'WP_Travel_FW_Field_Textarea';
		$field_types['date'] = 'WP_Travel_FW_Field_Date';
		return $field_types;
	}

	private function process() {
		$output = '';
		if ( ! empty( $this->fields ) ) {
			foreach ( $this->fields as $field ) {
				if ( array_key_exists( $field['type'], $this->field_types ) ) {

					$field['label'] = isset( $field['label'] ) ? $field['label'] : '';
					$field['name'] = isset( $field['name'] ) ? $field['name'] : '';
					$field['id'] = isset( $field['id'] ) ? $field['id'] : $field['name'];
					$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
					$field['placeholder'] = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
					$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
					$field['default'] = isset( $field['default'] ) ? $field['default'] : '';

					$field_init = new $this->field_types[ $field['type'] ];
					$content = $field_init->init( $field )->render( false );
					$output .= ( 'hidden' === $field['type'] ) ? $content : $this->template( $field, $content );
				}
			}
		}
		return $output;
	}

	function template( $field, $content ) {
		ob_start(); ?>
			<div class="wp-travel-form-field <?php echo esc_attr( $field['wrapper_class'] ); ?>">
				<label for="<?php echo esc_attr( $field['id'] ); ?>">
					<?php echo esc_attr( $field['label'] ); ?>
					<?php if ( isset( $field['validations']['required'] ) ) { ?>
						<span class="required-label">*</span>
					<?php } ?>
				</label>
				<?php echo $content; ?>
			</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	function render() {
		echo $this->process();
	}
}
