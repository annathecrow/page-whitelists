<?php

class WL_List {
	private $id;
	private $name;
	private $time;
	
	public function __construct($id, $name, $time) {
		$this->id = $id;
		$this->name = $name;
		$this->time = $time;
		
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_time() {
		return $this->time;
	}
	
	public function get_users() {
		//if not set $this->users get them from somewhere
		return "";
	}
	
	public function get_roles() {
		return "";
	}
	
	public function get_pages() {
		
	}
	
	public function create() {
		
	}
	
	public function update() {
		
	}
	
	public function delete() {
		
	}
}
