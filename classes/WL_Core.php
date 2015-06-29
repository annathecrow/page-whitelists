<?php

class Whitelists 
{
	private $data;
	private $access_manager;
	private $admin;
	private $basefile;
	
	public function __construct($data,$file) {
		$this->basefile = $file;
		$this->data = $data;
		$this->access_manager = new WL_Access_Manager($this->data);
		$this->admin = new WL_Admin($this->data,$file);
		
	}
	
	public function load_textdomain() {
		load_plugin_textdomain( 'page-whitelists', false, dirname( plugin_basename($this->basefile) ) . '/languages/' );
	}
	
	public function run() {		
		//filter hooks
		add_action( 'plugins_loaded', array($this, 'load_textdomain') );
		add_action('init',array($this->access_manager, 'access_check'));
		add_action('before_delete_post',array($this->data,'remove_page_from_all'));
		add_action('init',array($this->admin, 'admin_setup'));		
	}
	
	
	
}