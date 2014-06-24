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
			10, 
			'wl_lists', //the slug of the options page
			array($this,'render_lists_page')  
		);
// 		
		// add_options_page(
			// 'Whitelists', //label of the sidebar link
			// $this->menu_title, //title of the options page
			// 'manage_options', //what capability must a user have to use it
			// 'wl_settings', //the slug of the options page
			// array($this,'render_page') //function to render the page				
		// );
		
		add_submenu_page( 
			'wl_roles', 
			'Roles',
			'Roles', 
			10,
			'manage_options', 
			'$wl_roles', 
			array($this,'render_roles_page')
			);
		
		add_submenu_page( 
			'wl_lists', 
			'Settings',
			'Settings', 
			10,
			'manage_options', 
			'$wl_settings', 
			array($this,'render_settings_page')
			);
			
		
			
		
	}
	
	public function render_settings_page() {
		
	}
	
	public function render_roles_page() {
		//page where we will create/change/delete custom roles
	}
	
	public function render_lists_page() {
		require_once $this->template_path."admin_template.php";
	}
	
	
}
