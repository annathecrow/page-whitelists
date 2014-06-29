<?php

class WL_Data_Test extends WP_UnitTestCase {
	private $data;
	
	function __construct() {
		$this->data = New WL_Data();
		$this->data->initialize();
		
	}
	
	//toolbox
	
	private function can_edit($user_id,$list) {
		$has_cap = user_can($user_id,'edit_whitelist_'.$list->get_id());
		return $has_cap;
	}  
	
	private function role_can($role_name,$list) {
		$role = get_role($role_name);
		$role_can = array_key_exists('edit_whitelist_'.$list->get_id(), $role->capabilities);
		return $role_can;
	}
		
	//tests
	
	
	function test_create_whitelist_single() {
		$result = $this->data->create_whitelist("mango");	
		$this->assertInstanceOf('WL_List',$result);
	}
	
	function test_create_whitelist_multiples() {
		$this->data->create_whitelist("mango"); //mango once
		$result = $this->data->create_whitelist("mango"); //mango twice
		$this->assertTrue($result); //no two mangoes!
		
	}
	
	function test_get_whitelist() {
		$result = $this->data->create_whitelist("mango");
		$this->assertInstanceOf('WL_List',$result);
		$list = $this->data->get_whitelist($result->get_id());
		$this->assertInstanceOf('WL_List',$list);
	}
	
	function test_get_all_whitelists_count() {
		$this->data->create_whitelist("mango");
		$this->data->create_whitelist("apple");
		$array = $this->data->get_all_whitelists();
		$this->assertCount(2,$array);
	}
	
	function test_get_all_whitelists_type() {
		$this->data->create_whitelist("mango");
		$this->data->create_whitelist("apple");
		$array = $this->data->get_all_whitelists();
		foreach ($array as $list) {
			$this->assertInstanceOf('WL_List',$list); //this will try every member of $array and fail on first that doesn't pass
		}		
	}

	function test_delete_whitelist_success() {
		$result = $this->data->create_whitelist("mango");
		$success = $this->data->delete_whitelist($result->get_id());
		$this->assertTrue($success[0]);	
	}
}
