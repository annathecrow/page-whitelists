<?php

/**
 * creates settings page
 */
class WL_Admin {
	private $settings;
	private $data;
	
	function __construct(&$data,&$settings) {
		$this->settings = $settings;
		$this->data = $data;
	}
	
	
	public function add_menus() {
		add_menu_page( 
			$this->settings->get_plugin_title(), //label of the sidebar link
			$this->settings->get_plugin_title(), //title of the main options page
			'manage_options',
			'wl_lists', //the slug of the options page
			array($this,'render_lists_page')  
		);
		
		/*
		add_submenu_page( 
					'wl_lists', 
					'Roles',
					'Roles',
					'manage_options', 
					'$wl_roles', 
					array($this,'render_roles_page')
					);*/
		
		add_submenu_page( 
			'wl_lists', 
			'Settings',
			'Settings',
			'manage_options', 
			'$wl_settings', 
			array($this,'render_settings_page')
			);
		
	}
	
	public function enqueue_scripts($hook) {
		if( 'admin.php?page=wl_lists' != $hook ) return;
		wp_enqueue_script( 'wl_lists_js', $this->settings->get_template_path(). 'js/wl_lists.js' );
	}
	
 	public function render_settings_page() {
		require_once $this->settings->get_template_path()."settings_page.php";
		//whitelists as strict?
		//how to combine wlists
		//...???
	}
	
	public function render_roles_page() {
		require_once $this->settings->get_template_path()."roles_page.php";
		//load existing roles into a table
		//Create New...
			//a table of permissions, possibly with explanations?
			//assign whitelist
		//Edit
			//table of permissions
			//assigned whitelists - add, remove
		//Delete
		
		//should have similar look and feel as the rest of WP
		
	}
	
	public function render_lists_page() {
		
		$lists = $this->data->get_all_whitelists(); //returns all lists from the database as WL_List objects
		require_once $this->settings->get_template_path()."lists_page.php";

		//load existing whitelists
		//Create New...
			//add pages from a list (checkboxes?)
			//(possibly - add categories, tags)
			//assign to Roles
			//(assign to users -----> on Users page should be an option to add whitelist, too!)
		//Edit
			//assigned to Roles - add, remove
			//(assigned to Users - add, remove)
	}
	
	public function ajax_delete() {
		$success = 'true';
		die($success);
	}
	
	public function ajax_load() {
		//return data for edit form and an optional whitelist
	}
	
	public function ajax_save() {
		//create new whitelist from ajaxed data, or update an existing whitelist
		//needed: hidden field with id
		
	}
	
}
