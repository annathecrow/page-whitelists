<?php
/*
 * manages plugin settings - folders, names and other bullroar
 * 
 */
class WL_Settings {
	
	private $plugin_dir;
	private $template_path;
	private	$template_url;
	private	$plugin_title;
	
	public function __construct($file) {
		$this->plugin_dir = plugin_dir_path($file);
		$this->template_path = $this->plugin_dir."templates/";
		$this->template_url = plugin_dir_url($file)."templates/";
		$this->plugin_title = "Whitelists";
		
		
		//shouldn't all this shit be in wp options???	
	}
	
	public function get_template_path() {
		return $this->template_path;
	}
	
	public function get_plugin_title() {
		return $this->plugin_title;
	}
	
	public function get_template_url() {
		return $this->template_url;
	}
	
}
