<?php

	// Exit if accessed directly
	if ( !defined( 'ABSPATH' ) ) exit;

	if(!is_admin()) {
		add_action('init', 'sites_dashboard_advanced_search_run');
	}
	//add_action('admin_menu', 'sites_dashboard_advanced_search_admin_menu');  
	//add_action('network_admin_menu', 'sites_dashboard_advanced_search_network_admin_menu');  

	function sites_dashboard_advanced_search_run() {
		$sites_dashboard_search = get_blog_option(null, 'sites-advanced-search-enable');
		$search_string = @$GLOBALS['_GET']['s'];
		
		if($sites_dashboard_search === 'enabled' && !empty($search_string)){
			add_filter( 'posts_search', 'sites_dashboard_advanced_search', 500 );
		}
	}

	function sites_dashboard_advanced_search_network_admin_menu() {
 		//add_submenu_page('settings.php', 'Sites Advanced Search', 'Sites Advanced Search', 'manage_network_plugins', 'sites_dashboard_advanced_search', 'sites_dashboard_advanced_search_admin_page');
	}

	function sites_dashboard_advanced_search_admin_page() {
		return;
	}

	function sites_dashboard_db_filter_user_query( &$user_query ) {

	    if ( is_object( $user_query ) )
	        $user_query->query_where = str_replace( "user_nicename LIKE", "display_name LIKE", $user_query->query_where );
	    return $user_query;
	}

	function sites_dashboard_advanced_search( $where ) {

	    global $wpdb;

	    if ( empty( $where ))
	        return $where;
	    // get search expression
	    $terms = sanitize_text_field( get_query_var( 's' ) );
	    
	    // explode search expression to get search terms
	    $exploded = explode( ' ', $terms );
	    if( $exploded === FALSE || count( $exploded ) == 0 )
	        $exploded = array( 0 => $terms );

	    $where = '';
	    $where .= "AND ((({$wpdb->posts}.post_title LIKE '%$terms%')
	            OR (sites_strip_tags({$wpdb->posts}.post_content) LIKE '%$terms%'))";

	    foreach( $exploded as $key => $tag ) :
	        $where .= "
	            OR EXISTS (
	              SELECT * FROM {$wpdb->terms}
	              INNER JOIN {$wpdb->term_taxonomy}
	                ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
	              INNER JOIN {$wpdb->term_relationships}
	                ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
	              WHERE (
	                taxonomy = 'post_tag'
	                )
	                AND object_id = {$wpdb->posts}.ID
	                AND {$wpdb->terms}.name LIKE '%$tag%'
	            )";
	    endforeach;

	    $where .= "OR EXISTS (
	              SELECT * FROM {$wpdb->term_taxonomy}
	              INNER JOIN {$wpdb->term_relationships}
	                ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id
	              WHERE (
	                taxonomy = 'author'
	                )
	                AND object_id = {$wpdb->posts}.ID
	                AND {$wpdb->term_taxonomy}.description LIKE '%$terms%'
	            )";
		
	    if (function_exists('guest_author_url'))
	    {
	        $where .= "OR EXISTS (
	                  SELECT * FROM {$wpdb->postmeta}
	                  WHERE {$wpdb->postmeta}.meta_key = 'guest-author' AND {$wpdb->postmeta}.meta_value LIKE '%$terms%' AND {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
	                )";
	    }
	    add_filter( 'pre_user_query', 'sites_dashboard_db_filter_user_query' );
	    $args = array(
	        'count_total' => false,
	        'search' => sprintf( '*%s*', $terms ),
	        'search_fields' => array(
	            'display_name',
	            'user_login',
	        ),
	        'fields' => 'ID',
	    );
	    $matching_users = get_users( $args );
	    remove_filter( 'pre_user_query', 'sites_dashboard_db_filter_user_query' );

	    // Don't modify the query if there aren't any matching users
	    if ( empty( $matching_users ) )
	    {
	        $where .= ")";
	        return $where;
	    }

	    // Take a slightly different approach than core where we want all of the posts from these authors
	    $where .= " OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))";

	    return $where;

	}

?>