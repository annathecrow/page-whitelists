<?php

class Whitelists 
{
	public function __construct($data,$settings) {
		$this->data = $data;
		$this->settings = $settings;		
		$this->access_manager = new WL_Access_Manager();
	}
	
	public function install() {
		$this->data->initialize();
		//set up WP Options
		$this->data->create_whitelist("Dummy");

	}
	
	public function uninstall() {
		
	}
	
	public function get_path() {
		return plugin_dir_path(__FILE__);
	}
	
	public function init_admin_menu() {
		$this->admin_menu = new WL_Menu($this->settings->get_template_path());	
	}
	
	public function run() {		
		//filter hooks	
		add_action('admin_menu',array($this, 'init_admin_menu'));
		add_action('init',array($this->access_manager, 'access_check'));		
		// add_action('new_to_auto-draft',array($whitelists, 'auto_assign_to_whitelist'));		
	}
	
	
}