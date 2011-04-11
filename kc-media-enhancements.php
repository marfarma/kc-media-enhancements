<?php

/*
Plugin name: KC Media Enhancements
Plugin URI: http://kucrut.org/2011/04/kc-media-enhancements/
Description: Enhance WordPress media/attachment management
Version: 0.1
Author: Dzikri Aziz
Author URI: http://kucrut.org/
License: GPL v2
*/

class kcMediaEnhancements {
	function __construct() {
		$this->prefix = 'kc-media-enhancements';
		$this->inc_path = dirname(__FILE__) . '/kc-media-enhancements-inc';

		# i18n
		load_plugin_textdomain( $this->prefix, false, $this->prefix . '/kc-media-enhancements-inc/languages' );

		# Load helpers
		require_once( $this->inc_path . '/helpers.php' );

		add_filter( 'kc_plugin_settings', array(&$this, 'settings') );
		add_action( 'init', array(&$this, 'init'), 13 );
	}


	function init() {
		$this->options();
		$this->magick();

		//add_action( 'admin_footer', array(&$this, 'dev') );
	}


	function components() {
		$components = array(
			'insert_custom_size'	=> __('Enable insertion of images with custom sizes into post editor', $this->prefix),
			'taxonomies'					=> __('Enable taxonomies for attachments', $this->prefix)
		);

		return $components;
	}


	function options() {
		if ( function_exists('kc_get_option') ) {
			$this->options = kc_get_option( $this->prefix );
		}
		else {
			$options = array(
				'general' => array(
					'components'	=> array_map('__return_true', $this->components() ),
					'taxonomies'	=> array_map('__return_true', kc_get_public_taxonomies() )
				)
			);

			$this->options = apply_filters( 'kcme_options', $options );
		}
	}


	function settings( $groups ) {
		$my_group = array(
			'prefix'				=> $this->prefix,
			'menu_title'		=> __('KC Media Enhancements', $this->prefix),
			'page_title'		=> __('KC Media Enhancements Settings', $this->prefix),
			'options'				=> array(
				'general'		=> array(
					'id'			=> 'general',
					'title'		=> __('General', $this->prefix),
					'fields'	=> array(
						array(
							'id'			=> 'components',
							'title'		=> __('Components', $this->prefix),
							'type'		=> 'checkbox',
							'options'	=> $this->components()
						),
						array(
							'id'			=> 'taxonomies',
							'title'		=> __('Taxonomies for attachments', $this->prefix),
							'type'		=> 'checkbox',
							'options'	=> kc_get_public_taxonomies()
						)
					)
				)
			)
		);

		$groups[] = $my_group;
		return $groups;
	}


	function magick() {
		$components = $this->options['general']['components'];

		# 0. Insert image with custom sizes
		if ( isset($components['insert_custom_size']) && $components['insert_custom_size'] ) {
			require_once( $this->inc_path . '/insert_custom_size.php' );
			add_filter( 'attachment_fields_to_edit', 'kc_additional_image_size_input_fields', 11, 2 );
		}

		# 1. Attachment taxonomie
		if ( isset($components['taxonomies'])
					&& $components['taxonomies']
					&& isset($this->options['general']['taxonomies'])
					&& is_array($this->options['general']['taxonomies'])
					&& !empty($this->options['general']['taxonomies']) ) {

			$taxonomies = array();
			foreach ( $this->options['general']['taxonomies'] as $key => $val )
				if ( $val )
					$taxonomies[] = $key;

			if ( !empty($taxonomies) ) {
				require_once( $this->inc_path . '/attachment_taxonomies.php' );
				$do = new kcAttachmentTaxonomies( $taxonomies );
			}
		}
	}


	function dev() {
		echo '<pre>';


		echo '</pre>';
	}
}

$kcMediaEnhancements = new kcMediaEnhancements;

?>