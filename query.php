<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Filters_Query {

	public function __construct() {

		add_filter( 'jet-smart-filters/filter-instance/args', array( $this, 'preset_value' ), 0, 2 );
		add_filter( 'jet-smart-filters/query/vars', array( $this, 'register_filter_query_vars' ) );
		add_filter( 'jet-smart-filters/query/add-var', array( $this, 'process_filter_query_vars' ), 10, 4 );
		add_filter( 'jet-smart-filters/query/meta-query-row', array( $this, 'clear_meta_query' ) );

	}

	public function preset_value( $args, $filter_instance ) {
		
		return $args;

	}

	public function register_filter_query_vars( $vars ) {
		array_unshift( Plugin::$key );
		return $vars;
	}

	public function process_filter_query_vars( $value, $key, $var, $query ) {
		return $value;
	}

	public function clear_meta_query( $row ) {

		if ( false !== strpos( $row['key'], Plugin::$key ) ) {
			$row = array();
		}

		return $row;
	}

}
