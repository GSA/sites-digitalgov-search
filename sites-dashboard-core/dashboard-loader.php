<?php

/**
 * Sites Dashboard Core loader
 *
 * Core contains the commonly used functions, classes, and APIs.
 *
 * @package SitesDashboard
 * @subpackage Core
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Activation Hooks
register_activation_hook(__FILE__,'sites_dashboard_advanced_search_install');
register_activation_hook(__FILE__,'sites_dashboard_select_search_install');

function sites_dashboard_advanced_search_install() {
	add_site_blog_option(null, 'sites-advanced-search-enable', 'disabled');
	/*
    $result = mysql_query('CREATE FUNCTION `sites_dashboard_strip_tags`($str text) RETURNS text
    DETERMINISTIC
    READS SQL DATA
    BEGIN
        DECLARE $start, $end INT DEFAULT 1;
        LOOP
            SET $start = LOCATE("<", $str, $start);
            IF (!$start) THEN RETURN $str; END IF;
            SET $end = LOCATE(">", $str, $start);
            IF (!$end) THEN SET $end = $start; END IF;
            SET $str = INSERT($str, $start, $end - $start + 1, "");
        END LOOP;
    END;');

    if (!$result) {
        if(mysql_errno() == '1304')
            echo 'Cannot install custom mysql function.  It already exists';
        else
            die('Invalid query: ' . mysql_error());
    }
	*/
    return;
}

function sites_dashboard_select_search_install() {
	add_blog_option(null, 'sites-select-search', array('sites-select-search-status'=>'wordpress'));
}

// Decativation Hooks
function sites_dashboard_additional_rss_types_uninstall() {
	delete_blog_option(null, 'sites-additional-rss-types');
	unregister_setting( 'sites-additional-rss-types-group', 'sites-additional-rss-types' );
}

function sites_dashboard_advanced_search_uninstall() {
	delete_blog_blog_option(null, 'sites-advanced-search-enable');
    //$result = mysql_query('DROP FUNCTION `sites_dashboard_strip_tags`;');
}

function sites_dashboard_select_search_uninstall() {
	delete_blog_option(null, 'sites-select-search');
	unregister_setting('sites-select-search-group', 'sites-select-search');
}

register_deactivation_hook(__FILE__,'sites_dashboard_additional_rss_types_uninstall');
register_deactivation_hook(__FILE__,'sites_dashboard_advanced_search_uninstall');
register_deactivation_hook(__FILE__,'sites_dashboard_select_search_uninstall');

if( is_admin() ) {
	
	
	function register_sites_dashboard_additional_rss_types_settings() {
    	register_setting( 'sites-additional-rss-types-group', 'sites-additional-rss-types' );
	}
	add_action( 'admin_init', 'register_sites_dashboard_additional_rss_types_settings' );

	function register_sites_dashboard_select_search_settings() {
    	register_setting( 'sites-select-search-group', 'sites-select-search' );
	}
	add_action( 'admin_init', 'register_sites_dashboard_select_search_settings' );

	function sites_dashboard_admin_menu() {
	    add_submenu_page('options-general.php', 'Advanced Search Options', 'Advanced Search Options', 'manage_options', 'advanced_search_addons', 'sites_dashboard_page_main');
	}
	add_action('admin_menu', 'sites_dashboard_admin_menu');

}