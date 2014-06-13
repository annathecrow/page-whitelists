<?php

class Whitelists 
{
	public function __construct() {
		$this->data = new WL_Data();
		$this->access_manager = new WL_Access_Manager();
		$this->admin_menu = new WL_Admin_Menu();
	}
	
	public function install() {
		$this->data->initialize();
		$this->data->create_whitelist("Dummy");

	}
	
	public function uninstall() {
		
	}
	
	
	public function init_admin_menu() {
		
	}
	
	public function run() {		
		//filter hooks	
		// add action('admin_menu',array($this, 'admin_menu'));
		add_action('init',array($this->access_manager, 'access_check'));
		
		// add_action('new_to_auto-draft',array($whitelists, 'auto_assign_to_whitelist'));		
	}
	
	
}