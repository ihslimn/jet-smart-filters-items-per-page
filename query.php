<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Filters_Query {

	public $missing_vars = array();

	public $request = array();

	public function __construct() {

		add_filter( 'jet-smart-filters/filter-instance/args', array( $this, 'preset_value' ), 0, 2 );

		add_filter( 'jet-smart-filters/query/meta-query-row', array( $this, 'clear_meta_query' ) );

		add_filter( 'jet-smart-filters/query/vars', array( $this, 'remove_missing_vars' ) );

		add_filter( 'jet-smart-filters/query/request', array( $this, 'check_request' ), 0, 2 );

	}

	public function check_request( $request, $query_manager ) {

		$vars = array( 
			'sort', 
			'alphabet',
			'p',
			'author',
			'post_type',
		);

		$present_vars = array();

		if ( $query_manager->is_ajax_filter() ) {
			$data = $request['query'];
		} else {
			$data = $request;
		}

		foreach ( $data as $key => $value ) {

			if ( in_array( $key, $vars ) ) {
				$present_vars[] = $key;
			}

			if ( $this->is_item_to_process( $key ) ) {

				$this->set_items_per_page( $key, $value );
				
			}

		}

		$this->missing_vars = array_diff( $vars, $present_vars );

		return $request;

	}

	public function remove_missing_vars( $vars ) {
				
		foreach ( $vars as $index => $var ) {
			if ( in_array( $var, $this->missing_vars ) ) {
				unset( $vars[$index] );
			}
		}

		return $vars;

	}

	public function get_params_from_key( $key ) {
		
		$result = array();

		$params = explode( '__', $key );

		$result['suffix'] = $params[1] ?? '';

		$result['storage_type'] = $params[2] ?? 'session';

		return $result;

	}

	public function set_items_per_page( $key, $value = false ) {

		if ( ! $value ) {
			
			$args = explode( ':', $key ?? '' );

			$key = $args[0];

			$value = $args[1] ?? false;

		}

		if ( ! $value ) {
			return;
		}

		$params = $this->get_params_from_key( $key );
		
		Storage::set( 'items_per_page', $value, $params['suffix'], $params['storage_type'] );

	}

	public function preset_value( $args, $filter_instance ) {
		
		if ( false !== strpos( $args['query_var'], Plugin::$key ) ) {

			$params = explode( '__', $args['query_var'] );

			$key = 'items_per_page';

			$default = '';

			$suffix = $params[1] ?? '';

			$storage_type = $params[2] ?? 'session';

			$args['current_value'] = Storage::get( $key, $default, $suffix, $storage_type );

		}

		return $args;

	}

	public function clear_meta_query( $row ) {

		if ( $this->is_item_to_process( $row['key'] ) ) {
			$row = array();
		}

		return $row;
	}

	public function is_item_to_process( $key ) {
		return false !== strpos( $key, Plugin::$key );
	}

}
