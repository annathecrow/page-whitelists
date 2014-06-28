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
	
	public function get_users() {
		//if not set $this->users get them from somewhere
	}
	
	public function get_roles() {
		
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
