<?php
/*
 * manages plugin settings - folders, names and other bullroar
 * 
 */
class WL_Settings {
	
	public function __construct($template_path) {
		$this->template_path = $template_path;
		$this->menu_title = "Whitelists - Options";
		//shouldn't all this shit be in wp options???	
	}
	
	public function get_template_path() {
		return $this->template_path;
	}
	
	
}
