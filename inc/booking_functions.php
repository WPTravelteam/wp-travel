<?php

function wp_travel_booking_form_fields(){
	return apply_filters( 'wp_travel_booking_form_fields', 
		array(
		'first_name'	=>array('type'=>'text','label'=>'First Name','name'=>'first_name'),
		'middle_name'	=>array('type'=>'text','label'=>'Middle Name','name'=>'middle_name'),
		'last_name'		=>array('type'=>'text','label'=>'Last Name','name'=>'last_name'),
		// 'country'		=>array('type'=>'select','label'=>'Country','name'=>'country' , 'options' => wp_travel_get_countries() ),
		'address'		=>array('type'=>'text','label'=>'Address','name'=>'address'),
		'phone_number'	=>array('type'=>'text','label'=>'Phone Number','name'=>'phone_number'),
		'email'			=>array('type'=>'email','label'=>'Email','name'=>'email'),
		'pax'			=>array('type'=>'number','label'=>'Pax','name'=>'pax'),
	) );
}


