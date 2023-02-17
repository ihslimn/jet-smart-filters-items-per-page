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

		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'modify_query' ), 999 );

		add_filter( 'jet-smart-filters/query/request', array( $this, 'check_request' ), 0, 2 );

	}

	public function modify_query( $query ) {
		
		$_vars = array(
			'p',
		);
		
		foreach ( $this->missing_vars as $var ) {
			if ( in_array( $var, $_vars ) ) {
				unset( $query[ $var ] );
			}
		}

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
		
		if ( false !== strpos( $args['query_var'], Plugin::$key ) ) {

			$params = explode( '::', $args['query_var'] );

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

	public function is_item_to_process( $item ) {
		return false !== strpos( $item, Plugin::$key );
	}

}
