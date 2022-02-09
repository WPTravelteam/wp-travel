<?php
class WP_Travel_Helpers_Media {
	public static function get_attachment_meta_data( $id = false ) {
		if ( empty( $id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_ATTACHMENT_ID' );
		}
		$url = wp_get_attachment_url( $id );
		if ( empty( $url ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ATTACHMENT_NOT_FOUND' );
		}
		$attachment['url']    = $url;
		$attachment_meta_data = wp_get_attachment_metadata( $id );
		if ( ! empty( $attachment_meta_data ) ) {
			$upload_dir      = wp_get_upload_dir();
			$re              = '/^(.*\/)+(.*\.+.+\w)/m';
			$attachment_file = isset( $attachment_meta_data['file'] ) ? $attachment_meta_data['file'] : '';
			preg_match_all( $re, $attachment_file, $matches, PREG_SET_ORDER, 0 );
			$subfolder            = ! empty( $matches[0][1] ) ? $matches[0][1] : '';
			$full_attachment      = trailingslashit( $upload_dir['baseurl'] ) . $attachment_file;
			$attachment['id']     = $id;
			$attachment['width']  = isset( $attachment_meta_data['width'] ) ? $attachment_meta_data['width'] : '';
			$attachment['height'] = isset( $attachment_meta_data['height'] ) ? $attachment_meta_data['height'] : '';
			$attachment['url']    = $full_attachment;
			$attachment['file']   = $attachment_file;
			$attachment['sizes']  = isset( $attachment_meta_data['sizes'] ) ? $attachment_meta_data['sizes'] : '';
			if ( ! empty( $attachment_meta_data['sizes'] ) ) {
				$size_index = 0;
				foreach ( $attachment_meta_data['sizes'] as $size_key => $size ) {
					$attachment['sizes'][ $size_key ]        = $size;
					$attachment['sizes'][ $size_key ]['url'] = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $subfolder ) . $size['file'];
					$size_index++;
				}
			}
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_ATTACHMENT_DATA',
			array(
				'attachment' => $attachment,
			)
		);
	}
}
