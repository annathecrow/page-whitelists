<?php

class WL_Access_Manager {
	
	public function __construct() {
		
	}
	
	function on_edit_page() {
		if ( ! is_admin() || ! current_user_can( 'edit_pages' ) ) return FALSE;
		$s = get_current_screen();
		return ( $s instanceof WP_Screen && $s->id === 'edit-page' );
	}
			
	function on_edit_form() {
		if ( ! is_admin() || ! current_user_can( 'edit_pages' ) ) return FALSE;
		$s = get_current_screen();
		return ( $s instanceof WP_Screen && $s->id === 'page' );
	}
	
	function access_check() {
		WL_Dev::log("access check");
		//perform check based on user and page
		
		// add_action( 'load-edit.php', array($whitelists, 'filter_displayed') );
		// add_action('load-post.php', array($whitelists, 'filter_editable'));
		// add_action( 'wp_before_admin_bar_render', array($whitelists, 'filter_admin_bar') );
	}
}
