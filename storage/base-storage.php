<?php

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Storage {
	
	public static function get( $key, $value, $suffix = '', $storage_type = 'session' ) {

		$result = '';

		switch ( $storage_type ) {

			case 'session':
				$result = Session_Storage::instance()->get( $key, $value, $suffix );
				break;
			case 'cookies':
				$result = Cookies_Storage::instance()->get( $key, $value, $suffix );
				break;

		}

		return $result;

	}

	public static function set( $key, $value, $suffix = '', $storage_type = 'session' ) {

		$result = '';

		switch ( $storage_type ) {

			case 'session':
				Session_Storage::instance()->set( $key, $value, $suffix );
				break;
			case 'cookies':
				Cookies_Storage::instance()->set( $key, $value, $suffix );
				break;

		}

	}
	
}
