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
		add_filter( 'jet-smart-filters/query/vars', array( $this, 'register_filter_query_vars' ) );
		add_filter( 'jet-smart-filters/query/add-var', array( $this, 'process_filter_query_vars' ), 10, 4 );
		add_filter( 'jet-smart-filters/query/meta-query-row', array( $this, 'clear_meta_query' ) );

		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'modify_query' ) );

		add_filter( 'jet-smart-filters/query/request', array( $this, 'check_request' ), 0, 2 );

	}

	public function modify_query( $query ) {
		return $query;
		foreach ( $this->missing_vars as $var ) {
			unset( $query[ $var ] );
		}

		var_dump( $this->request );

		return $query;

	}

	public function set_items_per_page( $key, $value = false ) {

		$params = explode( '::', $key );

		$suffix = $params[1] ?? '';

		$storage_type = $params[2] ?? 'session';

		if ( ! $value ) {
			
			$args = explode( ':', $params[2] ?? array() );

			$storage_type = $args[0] ?? 'session';

			$value = $args[1] ?? false;

		}

		if ( ! $value ) {
			return;
		}

		Storage::set( 'items_per_page', $value, $suffix, $storage_type );

	}

	public function check_request( $request, $query_manager ) {

		$vars = $query_manager->query_vars();

		$present_vars = array();

		if ( $query_manager->is_ajax_filter() ) {

			foreach ( $request['query'] as $key => $value ) {

				if ( in_array( $key, $vars ) ) {
					$present_vars[] = $key;
				}

				if ( $this->is_item_to_process( $key ) ) {

					$this->set_items_per_page( $key, $value );
					
				}

			}

		} else {

			foreach ( $request as $key => $value ) {

				if ( in_array( $key, $vars ) ) {
					$present_vars[] = $key;
				}

			}

			$meta = $request[ 'meta' ] ?? false;

			if ( ! $meta ) {
				return $request;
			}

			$meta = explode( ';', $meta );

			foreach ( $meta as $key => $item ) {

				if ( $this->is_item_to_process( $item ) ) {

					$this->set_items_per_page( $item );

					unset( $meta[ $key ] );

				}

			}

			$request['meta'] = implode( ';', $meta );

		}

		$this->missing_vars = array_diff( $vars, $present_vars );

		return $request;

	}

	public function preset_value( $args, $filter_instance ) {
		
		return $args;

	}

	public function register_filter_query_vars( $vars ) {
		//array_unshift( $vars, Plugin::$key );
		return $vars;
	}

	public function process_filter_query_vars( $value, $key, $var, $query ) {
		//var_dump( $value, $key, $var );
		return $value;
	}

	public function clear_meta_query( $row ) {

		if ( $this->is_item_to_process( $row['key'] ) ) {
			$row = array();
		}

		return $row;
	}

	public function is_item_to_process( $item ) {
		return false !== strpos( $item, Plugin::$key );
	}

}
