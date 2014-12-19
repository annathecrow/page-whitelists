<?php

/**
 * creates settings page
 */
class WL_Menu {
	
	function __construct($template_path) {
		$this->template_path = $template_path;
		$this->menu_title= "Whitelists";
		
		add_menu_page( 
			'Whitelists', //label of the sidebar link
			$this->menu_title, //title of the options page
			'manage_options',
			'wl_lists', //the slug of the options page
			array($this,'render_lists_page')  
		);
		
		add_submenu_page( 
			'wl_lists', 
			'Roles',
			'Roles',
			'manage_options', 
			'$wl_roles', 
			array($this,'render_roles_page')
			);
		
		add_submenu_page( 
			'wl_lists', 
			'Settings',
			'Settings',
			'manage_options', 
			'$wl_settings', 
			array($this,'render_settings_page')
			);
			
		
			
		
	}
	
	public function render_settings_page() {
		require_once $this->template_path."settings_page.php";
		//whitelists as strict?
		//how to combine wlists
		//...???
	}
	
	public function render_roles_page() {
		require_once $this->template_path."roles_page.php";
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
		require_once $this->template_path."lists_page.php";
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
	
	
}
