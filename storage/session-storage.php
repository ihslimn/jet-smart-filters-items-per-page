<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Session_Storage {
	
	private static $instance = null;
	
	public $key = 'je_query_filters';
	
	public function __construct() {
		add_action( 'parse_request', array( $this, 'init_session' ) );
	}
	
	public function init_session( $wp ) {
		$this->start_session();
	}
	
	public function start_session() {

		if ( headers_sent() ) {
			return;
		}

		if ( ! session_id() ) {
			session_start();
		}

	}
	
	public function set( $key, $value, $suffix = '' ) {

		$this->start_session();

		if ( empty( $_SESSION[ $this->key ] ) ) {
			$_SESSION[ $this->key ] = array();
		}

		$_SESSION[ $this->key ][ $key . $suffix ] = $value;

	}
	
	public function get( $key, $default = false, $suffix = '' ) {

		$this->start_session();

		if ( empty( $_SESSION[ $this->key ] ) ) {
			$_SESSION[ $this->key ] = array();
		}

		return isset( $_SESSION[ $this->key ][ $key . $suffix ] ) ? $_SESSION[ $this->key ][ $key . $suffix ] : $default;

	}
	
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
}
