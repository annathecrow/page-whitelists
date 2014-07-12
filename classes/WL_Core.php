<?php

class Whitelists 
{
	public function __construct($data,$settings) {
		$this->data = $data;
		$this->settings = $settings;		
		$this->access_manager = new WL_Access_Manager($this->data);
		$this->admin = new WL_Admin($this->data,$this->settings);
		
	}
	
	public function install() {
		$this->data->initialize();
		//set up WP Options
		//TODO manage db versions somehow see tutorial on WP page
		//$this->data->create_whitelist("Dummy");

	}
	
	public function uninstall() {
		
	}
	
	public function run() {		
		//filter hooks	
		add_action('init',array($this->access_manager, 'access_check'));	
		add_action('admin_menu',array($this->admin, 'add_menus'));
		add_action('admin_enqueue_scripts',array($this->admin,'enqueue_scripts'));
		add_action('admin_init',array($this->admin,'register_ajax'));
		
		// add_action('new_to_auto-draft',array($whitelists, 'auto_assign_to_whitelist'));		
	}
	
	
}