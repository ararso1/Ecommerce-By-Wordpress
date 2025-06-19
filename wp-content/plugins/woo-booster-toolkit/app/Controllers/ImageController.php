<?php

namespace WCBT\Controllers;

/**
 * ImageController
 */
class ImageController {
	public function __construct() {
		add_filter( 'image_downsize', array( $this, 'download_image_size' ), 10, 3 );
	}

	/**
	 *  https://github.com/gambitph/WP-OTF-Regenerate-Thumbnails/blob/master/otf_regen_thumbs.php
	 * @param $out
	 * @param $id
	 * @param $size
	 *
	 * @return array|false
	 */
	public function download_image_size( $out, $id, $size ) {
		// If image size exists let WP serve it like normally
		$imagedata = wp_get_attachment_metadata( $id );

		// Image attachment doesn't exist
		if ( ! is_array( $imagedata ) ) {
			return false;
		}

		// If the size given is a string / a name of a size
		if ( is_string( $size ) ) {

			// If WP doesn't know about the image size name, then we can't really do any resizing of our own
			if ( empty( $allSizes[ $size ] ) ) {
				return false;
			}

			$att_url = wp_get_attachment_url( $id );
			if ( $att_url ) {
				$check_file = wp_check_filetype( $att_url );
				if ( in_array( $check_file['ext'], array( 'jpg', 'png', 'jpeg', 'gif', 'bmp', 'tif' ), true ) ) {

					// If the size has already been previously created, use it
					if ( ! empty( $imagedata['sizes'][ $size ] ) && ! empty( $allSizes[ $size ] ) ) {

						// But only if the size remained the same
						if ( $allSizes[ $size ]['width'] == $imagedata['sizes'][ $size ]['width']
								&& $allSizes[ $size ]['height'] == $imagedata['sizes'][ $size ]['height'] ) {
							return false;
						}

						// Or if the size is different and we found out before that the size really was different
						if ( ! empty( $imagedata['sizes'][ $size ]['width_query'] )
								&& ! empty( $imagedata['sizes'][ $size ]['height_query'] ) ) {
							if ( $imagedata['sizes'][ $size ]['width_query'] == $allSizes[ $size ]['width']
								&& $imagedata['sizes'][ $size ]['height_query'] == $allSizes[ $size ]['height'] ) {
								return false;
							}
						}
					}

					// Resize the image
					$resized = image_make_intermediate_size(
						get_attached_file( $id ),
						$allSizes[ $size ]['width'],
						$allSizes[ $size ]['height'],
						$allSizes[ $size ]['crop']
					);

					// Resize somehow failed
					if ( ! $resized ) {
						return false;
					}

					// Save the new size in WP
					$imagedata['sizes'][ $size ] = $resized;

					// Save some additional info so that we'll know next time whether we've resized this before
					$imagedata['sizes'][ $size ]['width_query']  = $allSizes[ $size ]['width'];
					$imagedata['sizes'][ $size ]['height_query'] = $allSizes[ $size ]['height'];

					wp_update_attachment_metadata( $id, $imagedata );

					// Serve the resized image

					return array(
						dirname( $att_url ) . '/' . $resized['file'],
						$resized['width'],
						$resized['height'],
						true,
					);
				}
			}

			// If the size given is a custom array size
		} elseif ( is_array( $size ) ) {
			$att_url = wp_get_attachment_url( $id );
			if ( $att_url ) {
				$check_file = wp_check_filetype( $att_url );
				if ( in_array( $check_file['ext'], array( 'jpg', 'png', 'jpeg', 'gif', 'bmp', 'tif' ), true ) ) {

					$imagePath = get_attached_file( $id );

					$crop       = array_key_exists( 2, $size ) ? $size[2] : true;
					$new_width  = $size[0];
					$new_height = $size[1];
					// If crop is false, calculate new image dimensions
					if ( ! $crop ) {
						if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
							add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );
							$trueData = wp_get_attachment_image_src( $id, 'large' );

						} else {
							$trueData = wp_get_attachment_image_src( $id, 'large' );
						}
						if ( $trueData[1] > $trueData[2] ) {
							// Width > height
							$ratio      = $trueData[1] / $size[0];
							$new_height = round( $trueData[2] / $ratio );
							$new_width  = $size[0];
						} else {
							// Height > width
							$ratio      = $trueData[2] / $size[1];
							$new_height = $size[1];
							$new_width  = round( $trueData[1] / $ratio );
						}
					}
					// This would be the path of our resized image if the dimensions existed
					$imageExt  = pathinfo( $imagePath, PATHINFO_EXTENSION );
					$imagePath = preg_replace( '/^(.*)\.' . $imageExt . '$/', sprintf( '$1-%sx%s.%s', $new_width, $new_height, $imageExt ), $imagePath );

					// If it already exists, serve it
					if ( file_exists( $imagePath ) ) {
						return array(
							dirname( $att_url ) . '/' . basename( $imagePath ),
							$new_width,
							$new_height,
							$crop,
						);
					}
					// If not, resize the image...
					$resized = image_make_intermediate_size(
						get_attached_file( $id ),
						$size[0],
						$size[1],
						$crop
					);

					$imagedata = wp_get_attachment_metadata( $id );
					// Resize somehow failed
					if ( $imagedata['width'] < $new_width && $imagedata['height'] < $new_height ) {
						return array( $att_url, $imagedata['width'], $imagedata['height'], false );
					}
					// Resize somehow failed
					if ( ! $resized ) {
						return false;
					}
					// Get attachment meta so we can add new size
					// Save the new size in WP so that it can also perform actions on it
					$imagedata['sizes'][ $size[0] . 'x' . $size[1] ] = $resized;
					wp_update_attachment_metadata( $id, $imagedata );

					// Then serve it
					return array(
						dirname( $att_url ) . '/' . $resized['file'],
						$resized['width'],
						$resized['height'],
						$crop,
					);
				}
			}
		}

		return false;
	}
}
