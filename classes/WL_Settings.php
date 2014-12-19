<?php
/*
 * manages plugin settings - folders, names and other bullroar
 * 
 */
class WL_Settings {
	
	public function __construct($dir) {
		$this->plugin_dir = $dir;
		$this->template_path = $dir."/templates/";
		$this->plugin_title = "Whitelists";
		
		
		//shouldn't all this shit be in wp options???	
	}
	
	public function get_template_path() {
		return $this->template_path;
	}
	
	public function get_plugin_title() {
		return $this->plugin_title;
	}
	
}
