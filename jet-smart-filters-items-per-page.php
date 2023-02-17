<?php
/**
 * Plugin Name: JetSmartFilters - Items Per Page
 * Plugin URI:
 * Description: 
 * Version:     1.0.0
 * Author:      
 * Author URI:  
 * Text Domain: 
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

namespace JSF_Items_Per_Page;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

if ( ! class_exists( '\JSF_Items_Per_Page\Plugin' ) ) {

	class Plugin {

		public static $key = 'jsf_items_per_page';

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}

		public function init() {

			if ( ! function_exists( 'jet_engine' ) || ! function_exists( 'jet_smart_filters' ) ) {

				add_action( 'admin_notices', function() {
					$class = 'notice notice-error';
					$message = '<b>WARNING!</b> <b>JetSmartFilters - Items Per Page</b> plugin requires <b>Jet Smart Filters</b> and <b>Jet Engine</b> plugins to work properly!';
					printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
				} );

				return;

			}

			//method_exists( jet_smart_filters()->utils, 'array_insert_after' )

			add_action( 'after_setup_theme', array( $this, 'init_components' ), 0 );

		}

		public function init_components() {

			$this->path = plugin_dir_path( __FILE__ );

			require $this->path . '/storage/base-storage.php';
			require $this->path . '/storage/session-storage.php';
			require $this->path . '/storage/cookies-storage.php';

			add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

			require $this->path . '/admin/dynamic-query.php';

			new Dynamic_Query();

			require $this->path . 'query.php';

			new Filters_Query();

		}

		public function register_macros() {

			require $this->path . '/macros/items-per-page.php';

			new Items_Per_Page_Macro();

		}

	}

}

new Plugin();
