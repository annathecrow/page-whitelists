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
		//$GLOBALS['silent'] = false;
		$this->data->create_whitelist("mango"); //mango once
		$result = $this->data->create_whitelist("mango"); //mango twice
		//$GLOBALS['silent'] = true;
		$this->assertTrue($result); //no two mangoes!
		
	}
	
	function test_get_whitelist() {
		$result = $this->data->create_whitelist("mango");
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
	
	
	function test_get_roles() {
		$roles_array = get_editable_roles();
		foreach ($roles_array as $role_array) {
			$role_object = get_role(strtolower($role_array['name']));
			$this->assertInstanceOf('WP_Role',$role_object);
			//$this->assertEquals()
		}
	}
	
	function test_delete_whitelist_success() {
		$result = $this->data->create_whitelist("mango");
		$GLOBALS['silent'] = false;
		$success = $this->data->delete_whitelist($result->get_id());
		$GLOBALS['silent'] = true;
		$this->assertTrue($success[0]);	
	}
	
	//user tests
	
	function test_user_exists() {
		$user_id = $this->factory->user->create();
		$user = get_user_by('id',$user_id);
		$this->assertInstanceOf('WP_User',$user);
	}
	
	function test_add_cap() {
		$user_id = $this->factory->user->create();
		$user = get_user_by('id',$user_id);
		$user->add_cap('edit_whitelist');
		$this->assertTrue(user_can($user->ID,'edit_whitelist'));
	}
	
	function test_add_whitelist_user() {
		$result = $this->data->create_whitelist("mango");
		$user_id = $this->factory->user->create();
		$this->data->add_whitelist_user($result->get_id(),$user_id);
		$this->assertTrue($this->can_edit($user_id,$result));
	}
	
	function test_remove_whitelist_user() {
		$result = $this->data->create_whitelist("mango");
		$user_id = $this->factory->user->create();
		$this->data->add_whitelist_user($result->get_id(),$user_id);
		$this->assertTrue($this->can_edit($user_id,$result));
		$this->data->remove_whitelist_user($result->get_id(),$user_id);
		$this->assertFalse($this->can_edit($user_id,$result));
	}
	
	function test_add_whitelist_role() {
		//$this->markTestSkipped('not doing this now');
		$result = $this->data->create_whitelist("mango");
		$this->data->add_whitelist_role($result->get_id(),'editor');
		$this->assertTrue($this->role_can('editor',$result));
	}
	
	function test_remove_whitelist_role() {
		$result = $this->data->create_whitelist("mango");
		$role_name = 'editor';
		$this->data->add_whitelist_role($result->get_id(),$role_name);
		$this->assertTrue($this->role_can($role_name,$result));
		$this->data->remove_whitelist_role($result->get_id(),$role_name);
		$this->assertFalse($this->role_can($role_name,$result));
	}
}
