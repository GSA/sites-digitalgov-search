<?php
/*
    Plugin Name: Sites Additional RSS Feed Types
    Plugin URI: http://www.sites.usa.gov
    Description: Created for use by Wordpress sites running on  <a href="http://www.sites.usa.gov" title="Sites.USA.Gov">Sites.USA.Gov</a>.
    Author: Sites.USA.Gov
    Version: 1.0.1
    Author URI: http://www.gsa.gov

    Sites Additional RSS Feed Types is released under GPL:
    http://www.opensource.org/licenses/gpl-license.php
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function sites_dashboard_additional_rss_types_func($qv) {
	
	 if (isset($qv['feed']) && !isset($qv['post_type']))
        $qv['post_type'] = sites_dashboard_get_additional_rss_types();
    
    return $qv;
}
add_filter('request', 'sites_dashboard_additional_rss_types_func');

function sites_dashboard_get_additional_rss_types() {

	$options = get_blog_option(null, 'sites-additional-rss-types');
	$final_rss_types = array('post');
	$post_types = get_post_types( array('public' => true), 'names' ); 
	
	foreach ( $post_types as $post_type ) {
		if(isset($options[$post_type]) && $options[$post_type] == 'Y')
		{
			$final_rss_types[] = $post_type;
		}
	}
	return $final_rss_types;
}

?>