<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Cookies_Storage {
	
	private static $instance = null;
	
	public $key = 'je_query_filters';
	
	public $current_set = array();
	
	public function __construct() {
		return;
	}
	
	public function set( $key, $value, $suffix = '' ) {
		
		$this->current_set = array(
			'key'    => $key,
			'suffix' => $suffix,
			'value'  => $value,
		);
		
		$suffix = '_' . $suffix;
		
		$cookie_value = $_COOKIE[ $this->key ] ?? '';
		
		if ( empty( $cookie_value ) ) {
			$result = $key . $suffix . '=' . $value;
		} else {
			$cookie_array = explode( ',', $cookie_value );
			$set = false;
			foreach ( $cookie_array as $index => $pair ) {
				if ( 0 === strpos( $pair, $key . $suffix ) ) {
					$cookie_array[ $index ] = $key . $suffix . '=' . $value;
					$set = true;
					break;
				}
			}
			if ( ! $set ) {
				$cookie_array[] = $key . $suffix . '=' . $value;
			}
			$result = implode( ',', $cookie_array );
		}
		
		setcookie(
			$this->key,
			$result,
			0,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			true,
			false
		);

	}
	
	public function get( $key, $default = false, $suffix = '' ) {
		
		if ( 'jet_smart_filters' === ( $_REQUEST['action'] ?? '' ) && ! empty( $this->current_set ) && $this->current_set[ 'key' ] === $key && $this->current_set[ 'suffix' ] === $suffix ) {
			return $this->current_set[ 'value' ];
		}
		
		$suffix = '_' . $suffix;
		
		if ( ! isset( $_COOKIE[ $this->key ] ) || empty( $_COOKIE[ $this->key ] ) ) {
			return $default;
		}
		
		$cookie_value = explode( ',', $_COOKIE[ $this->key ] );
		
		$result = $default;
		
		foreach ( $cookie_value as $value ) {
			$pair = explode( '=', $value );
			if ( $pair[0] === $key . $suffix ) {
				$result = $pair[1] ?? $default;
				break;
			}
		}
		
		return $result;

	}
	
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
}
