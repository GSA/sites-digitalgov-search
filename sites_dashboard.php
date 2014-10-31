<?php
/**
 * Sites Dashboard Plugin
 *
 * Created for use by Wordpress sites running on Sites.USA.Gov.
 *
 *
 * @package SitesDashboard
 * @subpackage Main
 */

/*
    Plugin Name: Advanced Search Options
    Plugin URI: http://www.sites.usa.gov
    Description: Created for use by Wordpress sites running on  <a href="http://www.sites.usa.gov" title="Sites.USA.Gov">Sites.USA.Gov</a>.
    Author: Sites.USA.Gov
    Version: 1.0.1
    Author URI: http://www.gsa.gov

    Sites Search Addons is released under GPL:
    http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


if ( !class_exists( 'SitesDashboard' ) ) :
/**
 * Main SitesDashboard Class
 *
 * @since SitesDashboard (1.0.1)
 */
class SitesDashboard {
	/**
	 *
	 * @see SitesDashboard::setup_globals()
	 * @var array
	 */
	private $data;

	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new SitesDashboard;
			$instance->constants();
			$instance->setup_globals();
			$instance->includes();
		}

		// Always return the instance
		return $instance;
	}
	/** Private Methods ***************************************************/

	/**
	 * Bootstrap constants.
	 *
	 * @since SitesDashboard (1.0.1)
	 *
	 * @uses is_multisite()
	 * @uses get_current_site()
	 * @uses get_current_blog_id()
	 * @uses plugin_dir_path()
	 * @uses plugin_dir_url()
	 */

	private function constants() {

		// Place your custom code (actions/filters) in a file called
		// '/plugins/bp-custom.php' and it will be loaded before anything else.
		if ( file_exists( WP_PLUGIN_DIR . '/bp-custom.php' ) )
			require( WP_PLUGIN_DIR . '/bp-custom.php' );

		// Path and URL
		if ( ! defined( 'SITES_DASHBOARD_PLUGIN_DIR' ) ) {
			define( 'SITES_DASHBOARD_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		}

		if ( ! defined( 'SITES_DASHBOARD_PLUGIN_URL' ) ) {
			$plugin_url = plugin_dir_url( __FILE__ );

			// If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
			if ( is_ssl() )
				$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

			define( 'SITES_DASHBOARD_PLUGIN_URL', $plugin_url );
		}

		// Define on which blog ID SitesDashboard should run
		if ( ! defined( 'SITES_DASHBOARD_ROOT_BLOG' ) ) {

			// Default to use current blog ID
			// Fulfills non-network installs and BP_ENABLE_MULTIBLOG installs
			$root_blog_id = get_current_blog_id();

			// Multisite check
			if ( is_multisite() ) {

				// Multiblog isn't enabled
				if ( ! defined( 'SITES_DASHBOARD_ENABLE_MULTIBLOG' ) || ( defined( 'SITES_DASHBOARD_ENABLE_MULTIBLOG' ) && (int) constant( 'SITES_DASHBOARD_ENABLE_MULTIBLOG' ) === 0 ) ) {
					// Check to see if BP is network-activated
					// We're not using is_plugin_active_for_network() b/c you need to include the
					// /wp-admin/includes/plugin.php file in order to use that function.

					// get network-activated plugins
					$plugins = get_site_option( 'active_sitewide_plugins');

					// basename
					$basename = plugin_basename( constant( 'SITES_DASHBOARD_PLUGIN_DIR' ) . 'sites_dashboard.php' );

					// plugin is network-activated; use main site ID instead
					if ( isset( $plugins[ $basename ] ) ) {
						$current_site = get_current_site();
						$root_blog_id = $current_site->blog_id;
					}
				}

			}

			define( 'SITES_DASHBOARD_ROOT_BLOG', $root_blog_id );
		}

		// Whether to refrain from loading deprecated functions
		if ( ! defined( 'SITES_DASHBOARD_IGNORE_DEPRECATED' ) ) {
			define( 'SITES_DASHBOARD_IGNORE_DEPRECATED', false );
		}

		// The search slug has to be defined nice and early because of the way
		// search requests are loaded
		//
		// @todo Make this better
		if ( !defined( 'SITES_DASHBOARD_SEARCH_SLUG' ) )
			define( 'SITES_DASHBOARD_SEARCH_SLUG', 'search' );
	}

	/**
	 * Component global variables.
	 *
	 * @since SitesDashboard (1.0.1)
	 * @access private
	 *
	 * @uses plugin_dir_path() To generate SitesDashboard plugin path.
	 * @uses plugin_dir_url() To generate SitesDashboard plugin url.
	 * @uses apply_filters() Calls various filters.
	 */
	private function setup_globals() {

		/** Versions **************************************************/

		$this->version    = '1.0.1';

		/** Paths******************************************************/

		// SitesDashboard root directory
		$this->file           = __FILE__;
		$this->basename       = plugin_basename( $this->file );
		$this->plugin_dir     = SITES_DASHBOARD_PLUGIN_DIR;
		$this->plugin_url     = SITES_DASHBOARD_PLUGIN_URL;

	}

	/**
	 * Include required files.
	 *
	 * @since SitesDashboard (1.0.1)
	 * @access private
	 *
	 * @uses is_admin() If in WordPress admin, load additional file.
	 */
	private function includes() {

		require( $this->plugin_dir . 'sites-dashboard-core/dashboard-loader.php'    );
		require( $this->plugin_dir . 'sites-dashboard-funcs/additional-rss-types.php'    );
		require( $this->plugin_dir . 'sites-dashboard-funcs/advanced-search.php'    );
		require( $this->plugin_dir . 'sites-dashboard-core/dashboard-pages.php'    );
		
	}
}

/**
 * @return SitesDashboard The one true SitesDashboard Instance.
 */

function sites_dashboard() {
	return SitesDashboard::instance();
}

/**
 * Hook SitesDashboard early onto the 'plugins_loaded' action..
 *
 * This gives all other plugins the chance to load before SitesDashboard, to get
 * their actions, filters, and overrides setup without SitesDashboard being in the
 * way.
 */
if ( defined( 'SITES_DASHBOARD_LATE_LOAD' ) ) {
	add_action( 'plugins_loaded', 'sites_dashboard', (int) SITES_DASHBOARD_LATE_LOAD );

// "And now here's something we hope you'll really like!"
} else {
	$GLOBALS['sites-dashboard'] = sites_dashboard();
}

endif;
