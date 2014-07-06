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
		//$this->data->create_whitelist("mango");
		$this->data->create_whitelist("mango");
		$this->data->create_whitelist("grape");
		$this->data->create_whitelist("radish");
		$this->data->create_whitelist("millenium");
		$this->data->create_whitelist("junior");
		
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
		WL_Dev::log("trying to enqueue scripts");
		$screen = get_current_screen(); 
		if($screen->id != 'toplevel_page_wl_lists') {
			WL_Dev::log("not on the right page");
			return;
		}
		$script_path = $this->settings->get_template_url(). 'js/wl_lists.js';
		wp_enqueue_script('wl_lists_js', $script_path, array('jquery'),'1.0.0',true);
	}
	
	public function register_ajax() {
		WL_Dev::log('registering ajax');
		add_action('wp_ajax_wl_delete', array($this,'ajax_delete'));
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
		$id = $_POST['id'];
		$result = $this->data->delete_whitelist($id);
		if ($result[0]) {
			$reply = 'success';
		} else {
			$reply = $result[1];
		}
		die($reply);
	}
	
	public function ajax_load() {
	}

}