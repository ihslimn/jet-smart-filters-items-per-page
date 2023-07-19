<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Dynamic_Query {

	public function __construct() {

		add_action( 'jet-smart-filters/admin/register-dynamic-query', array( $this, 'helper_dynamic_query' ) );

	}

	public function helper_dynamic_query( $dynamic_query_manager ) {

		$dynamic_query_item = new class( Plugin::$key, 'Jet Smart Filters - Items Per Page' ) {
			
			private $label;
			
			public function __construct( $key, $label ) {
				$this->key     = $key;
				$this->label   = $label;
			}

			public function get_name() {
				return $this->key;
			}

			public function get_label() {
				return $this->label;
			}

			public function get_extra_args() {

				return array(

					'storage_key' => array(
						'type'        => 'text',
						'title'       => 'Storage key',
					),
					'storage_type' => array(
						'type'        => 'select',
						'title'       => 'Storage type',
						'options'     => array(
							'session' => 'Session',
							'cookies' => 'Cookies',
						),
					),

				);

			}

			public function get_delimiter() {
				return '__';
			}

		};
		
		$dynamic_query_manager->register_item( $dynamic_query_item );
		
	}
	

}
