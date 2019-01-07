<?php
class WP_Travel_FW_Field_Country_Dropdown extends WP_Travel_FW_Field_Select {
	protected $field_type = 'country_dropdown';

	function init( $field ) {
		$this->field = $field;
		$this->field['options'] = wp_travel_get_countries();
		return $this;
	}
}
