<?php

class WL_List_Test extends WP_UnitTestCase {
	private $list;
	private $data;
	
	function __construct() {
		//$GLOBALS['silent'] = false;
		$this->data = New WL_Data();
		$this->data->initialize();
		$this->list = new WL_List(1,'cocoNOT',date("Y-m-d H:i:s"));
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
	
	function test_get_users() {
		//$this->markTestIncomplete();
		$this->factory->user->create_many(5);
		$user_ids = $this->factory->user->create_many(3);
		foreach ($user_ids as $user_id) {
			$this->list->add_user($user_id);
		}
		$users = $this->list->get_users();
		$this->assertEquals(sizeof($user_ids),sizeof($users));
		//compare arrays
	}
	
	function test_get_roles() {
		$this->markTestIncomplete();
	}
	
	function test_add_user() {
		$user_id = $this->factory->user->create();
		$this->list->add_user($user_id);
		$this->assertTrue($this->can_edit($user_id,$this->list));
	}
	
	function test_remove_user() {
		$user_id = $this->factory->user->create();
		$this->list->add_user($user_id);
		$this->assertTrue($this->can_edit($user_id,$this->list));
		$this->list->remove_user($user_id);
		$this->assertFalse($this->can_edit($user_id,$this->list));
	}
	
	function test_add_role() {
		$success = $this->list->add_role('editor');
		$this->assertTrue($success);
		$this->assertTrue($this->role_can('editor',$this->list));
	}
	
	function test_remove_role() {
		$role_name = 'editor';
		$success = $this->list->add_role('editor');
		$this->assertTrue($this->role_can('editor',$this->list));
		$success = $this->list->remove_role('editor');
		$this->assertFalse($this->role_can('editor',$this->list));
	}
	
}