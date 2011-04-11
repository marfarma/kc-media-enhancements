<?php

if ( !function_exists('kc_get_additional_image_sizes') ) {
	/**
	 * Get additional image sizes
	 *
	 * @return array Custom image sizes
	 */
	function kc_get_additional_image_sizes() {
		$sizes = array();
		global $_wp_additional_image_sizes;
		if ( isset($_wp_additional_image_sizes) && count($_wp_additional_image_sizes) ) {
			$sizes = apply_filters( 'intermediate_image_sizes', $_wp_additional_image_sizes );
			$sizes = apply_filters( 'kc_get_additional_image_sizes', $_wp_additional_image_sizes );
		}

		return $sizes;
	}
}


function kc_get_public_taxonomies() {
	$public_taxonomies = array();
	$taxonomies = get_taxonomies( array('public' => true),  'objects');
	if ( empty($taxonomies) )
		return $public_taxonomies;

	foreach ( $taxonomies as $tax )
		if ( $tax->name != 'post_format' )
			$public_taxonomies[$tax->name] = $tax->label;

	return $public_taxonomies;
}

?>