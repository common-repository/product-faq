<?php
/**
 * Plugin Name:       Product FAQ for Woocommerce
 * Plugin URI:        https://wordpress.org/plugins/product-faq
 * Description:       Adding uniq FAQ for each download
 * Version:           1.1
 * Author:            Wow-Company
 * Author URI:        https://wow-estore.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-product-faq
 */


namespace woo_product_faq;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WP_Plugin' ) ) :

	/**
	 * Main WP_Plugin Class.
	 *
	 * @since 1.0
	 */
	final class WP_Plugin {

		private static $_instance;

		/**
		 * Wow Plugin information
		 *
		 * All information which need for correctly plugin working
		 *
		 * @return array
		 * @static
		 */
		private static function _plugin_info() {

			$info = array(
				'plugin' => array(
					'name'      => 'Woocommerce FAQ', // Plugin name
					'author'    => 'Wow-Company', // Author
					'prefix'    => 'woo_product_faq', // Prefix for database
					'text'      => 'woo-product-faq',    // Text domain for translate files
					'version'   => '1.1', // Current version of the plugin
					'file'      => __FILE__, // Main file of the plugin
					'slug'      => 'woo-product-faq', // Name of the plugin folder
					'url'       => plugin_dir_url( __FILE__ ), // filesystem directory path for the plugin
					'dir'       => plugin_dir_path( __FILE__ ), // URL directory path for the plugin
				),

			);

			return $info;

		}

		/**
		 * Main WP_Plugin Instance.
		 *
		 * Insures that only one instance of WP_Plugin exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return object|WP_Plugin The one true WP_Plugin for Current plugin
		 *
		 * @uses      WP_Plugin::_includes() Include the required files.
		 * @uses      WP_Plugin::text_domain() load the language files.
		 * @since     1.0
		 * @static
		 * @staticvar array $_instance
		 */
		public static function instance() {

			if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof WP_Plugin ) ) {

				$info = self::_plugin_info();

				self::$_instance = new WP_Plugin;

				add_action( 'plugins_loaded', array( self::$_instance, 'text_domain' ) );

				self::$_instance->_includes();
				self::$_instance->admin  = new WP_Plugin_Admin( $info );
				self::$_instance->public = new WP_Plugin_Public( $info );

			}

			return self::$_instance;
		}

		/**
		 * Throw error on object clone.
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @return void
		 * @since  1.0
		 * @access protected
		 */
		public function __clone() {
			$info = self::_plugin_info();
			$text = $info['plugin']['text'];
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', $text ), '1.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @return void
		 * @since  1.0
		 * @access protected
		 */
		public function __wakeup() {
			$info = self::_plugin_info();
			$text = $info['plugin']['text'];
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', $text ), '1.0' );
		}


		/**
		 * Include required files.
		 *
		 * @access private
		 * @return void
		 * @since  1.0
		 */
		private function _includes() {
			include_once 'inc/class-admin.php';
			include_once 'inc/class-public.php';
		}


		/**
		 * Download the folder with languages.
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function text_domain() {
			$languages_folder = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			load_plugin_textdomain( 'woo-product-faq', false, $languages_folder );
		}

	}

endif; // End if class_exists check.

/**
 * The main function for that returns WP_Plugin
 *
 * @since 1.0
 */
function WP_Plugin_run() {
	return WP_Plugin::instance();
}

// Get Running.
WP_Plugin_run();
