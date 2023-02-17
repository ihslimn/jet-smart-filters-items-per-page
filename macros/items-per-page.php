<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Items_Per_Page_Macro extends \Jet_Engine_Base_Macros {

	public function macros_tag() {
		return 'jsf_items_per_page';
	}

	public function macros_name() {
		return 'Jet Smart Filters - Items Per Page';
	}
	
	public function macros_args() {
		return array(
			'jsf_items_per_page' => array(
				'label'   => 'Default Posts Per Page',
				'type'	  => 'text',
				'default' => '10',
			),
			'jsf_storage_key' => array(
				'label'   => 'Storage key',
				'type'	  => 'text',
			),
			'jsf_storage_type' => array(
				'label'   => 'Storage type',
				'type'	  => 'select',
				'default' => 'session',
				'options' => array(
					'session' => 'Session',
					'cookies' => 'Cookies',
				),
			),
		);
	}

	public function macros_callback( $args = array() ) {
		
		$items_per_page = $args['jsf_items_per_page'] ?? false;
		
		$items_per_page = absint( $items_per_page );
		
		$suffix = $args['jsf_storage_key'] ?? '';
		
		$storage_type = $args['jsf_storage_type'] ?? 'session';

		$result = Storage::get( 'items_per_page', $items_per_page, $suffix, $storage_type );
		
		return $result;
		
	}
}
