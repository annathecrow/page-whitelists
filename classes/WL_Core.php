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
		WL_Dev::log("loading textdomain");
		load_plugin_textdomain( 'whitelists', false, dirname( plugin_basename($this->basefile) ) . '/languages/' );
	}
	
	public function run() {		
		//filter hooks
		add_action( 'plugins_loaded', array($this, 'load_textdomain') );
		add_action('init',array($this->access_manager, 'access_check'));
		add_action('before_delete_post',array($this->data,'remove_page_from_all'));
		add_action('add_meta_boxes', array($this->admin, 'add_metabox'));
		add_action('save_post', array($this->admin, 'save_metabox'));
		add_action('admin_menu',array($this->admin, 'add_menus'));
		add_action('admin_enqueue_scripts',array($this->admin,'enqueue_assets'));
		add_action('admin_init',array($this->admin,'register_ajax'));
		
	}
	
	
	
}