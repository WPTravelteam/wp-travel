<?php 

add_action('woocommerce_loaded' , function (){ 
	class My_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Data_Store_Interface {


		/**
		 * Method to read a product from the database.
		 * @param WC_Product
		 */
		public function read( &$product ) {
			$product->set_defaults();
	
			if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || 'product' !== $post_object->post_type ) {
				//throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
			}
	
			$id = $product->get_id();
	
			$product->set_props( array(
				'name'              => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
				'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				'short_description' => $post_object->post_excerpt,
				'parent_id'         => $post_object->post_parent,
				'menu_order'        => $post_object->menu_order,
				'reviews_allowed'   => 'open' === $post_object->comment_status,
			) );
	
			$this->read_attributes( $product );
			$this->read_downloads( $product );
			$this->read_visibility( $product );
			$this->read_product_data( $product );
			$this->read_extra_data( $product );
			$product->set_object_read( true );
		}
	
	
	}
	
	add_filter( 'woocommerce_data_stores', 'my_woocommerce_data_stores' );
	
	function my_woocommerce_data_stores( $stores ) {
	
		// require_once WP_TRAVEL_ABSPATH . '/inc/classes/class-data-store-cpt.php';
		$stores['product'] = 'MY_Product_Data_Store_CPT';
	
		return $stores;
	}
	
	add_filter('woocommerce_product_get_price', 'my_woocommerce_product_get_price', 10, 2 );
	function my_woocommerce_product_get_price( $price, $product ) {
		global $wt_cart;
		$extras_price_total = 0;
		if ( get_post_type( $product->get_id() ) === 'itineraries' ) {
			if( isset( array_values($wt_cart->getItems())[0]['trip_extras'] ) && !empty( array_values($wt_cart->getItems())[0]['trip_extras'] ) ){
				foreach ( array_values($wt_cart->getItems())[0]['trip_extras']['id'] as $k => $extra_id ) :

					$trip_extras_data = get_post_meta( $extra_id, 'wp_travel_tour_extras_metas', true );

					$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
					$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

					if ( $sale_price ) {
						$price = $sale_price;
					}
					$price = WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $price );
					$qty   = isset( array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] ) && array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] ? array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] : 1;

					$total = $price * $qty;

					$extras_price_total = $extras_price_total + ( $price * $qty );

				endforeach;
			}
			## Price calculation ##
			if( isset(  array_values($wt_cart->getItems())[0]  ) ){
				$price = (double)array_values($wt_cart->getItems())[0]['trip_price'] + $extras_price_total;
			}
			
		}
		return $price;
	}

	// add_action( 'woocommerce_before_calculate_totals', 'wptravel_add_cart_price', 10, 1);

	// function wptravel_add_cart_price( $cart_object ) {

	// 	global $wt_cart;

	// 	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	// 		return;
	// 	$extras_price_total = 0;
		
	// 	foreach ( $cart_object->get_cart() as $cart_item ) {
			
	// 		if( isset( array_values($wt_cart->getItems())[0]['trip_extras'] ) && !empty( array_values($wt_cart->getItems())[0]['trip_extras'] ) ){
	// 			foreach ( array_values($wt_cart->getItems())[0]['trip_extras']['id'] as $k => $extra_id ) :

	// 				$trip_extras_data = get_post_meta( $extra_id, 'wp_travel_tour_extras_metas', true );

	// 				$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
	// 				$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

	// 				if ( $sale_price ) {
	// 					$price = $sale_price;
	// 				}
	// 				$price = WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $price );
	// 				$qty   = isset( array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] ) && array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] ? array_values($wt_cart->getItems())[0]['trip_extras']['qty'][ $k ] : 1;

	// 				$total = $price * $qty;

	// 				$extras_price_total = $extras_price_total + ( $price * $qty );

	// 			endforeach;
	// 		}
	// 		## Price calculation ##
	// 		$price = (double)array_values($wt_cart->getItems())[0]['trip_price'] + $extras_price_total;

	// 		$cart_item['data']->set_price( $price ); // WC 3.0+
	// 	}
	// }

} );


