<?php

class WL_List_Test extends WP_UnitTestCase {
	private $list;
	private $data;
	
	function __construct() {
		
		$this->data = New WL_Data();
		$this->data->initialize();
		$this->list = new WL_List(1,'cocoNOT',date("Y-m-d H:i:s"));
	}
	
	static function setupBeforeClass() {
		echo "\nWL_List tests: ";
	}
	
	static function tearDownAfterClass() {
		echo "\n";
	}
	
	//toolbox
	
	private function can_edit($user_id,$list) {
		$has_cap = user_can($user_id,$this->list->get_cap());
		return $has_cap;
	}  
	
	private function role_can($role_name,$list) {
		$role = get_role($role_name);
		$role_can = array_key_exists($this->list->get_cap(), $role->capabilities);
		return $role_can;
	}
	
	//tests
	
	function test_add_user() {
		$user_id = $this->factory->user->create();
		$this->list->add_user($user_id);
		$this->assertTrue($this->can_edit($user_id,$this->list));
		return $user_id;
	}
	
	/**
	 * @depends test_add_user
	 */
	function test_remove_user($user_id) {
		$this->list->remove_user($user_id);
		$this->assertFalse($this->can_edit($user_id,$this->list));
	}
	
	
	function test_get_users() {
		
		$user_ids = $this->factory->user->create_many(5);
		$user_ids = $this->factory->user->create_many(3);
		foreach ($user_ids as $user_id) {
			$this->list->add_user($user_id);
		}
		$returned = $this->list->get_users();
		$this->assertInternalType('array',$returned);
		$GLOBALS['silent'] = true;
		return array('assigned'=>$user_ids,'returned'=>$returned);
	}
	
	/**
	 * @depends test_get_users
	 */
	function test_get_users_length($users) {
		$this->assertEquals(sizeof($users['assigned']),sizeof($users['returned']));
		return ($users);
	}
	
	/**
	 * @depends test_get_users_length
	 */	
	function test_get_users_type($users) {
		$is_type = true;
		foreach ($users['returned'] as $user) {
			$is_type = (get_class($user)==='WP_User');
		}
		$this->assertTrue($is_type);
	}
	
	
	
	
	
	function test_add_role_success() {
		$role_name = 'editor';
		$success = $this->list->add_role($role_name);
		$this->assertTrue($success);
		return $role_name;
		
	}
	
	/**
	 * @depends test_add_role_success
	 */
	function test_add_role_has_cap($role_name) {
		$this->assertTrue($this->role_can($role_name,$this->list));
		return $role_name;
	}
	
	/**
	 * @depends test_add_role_has_cap
	 */	
	function test_remove_role_success($role_name) {
		$success = $this->list->remove_role($role_name);
		$this->assertTrue($success);
		return $role_name;
	}
	
	/**
	 * @depends test_remove_role_success
	 */
	function test_remove_role_no_cap($role_name) {
		$this->assertFalse($this->role_can($role_name,$this->list));
	}
	
	function test_get_roles() {
		$assign = array('editor','contributor');
		foreach($assign as $role_name) {
			$this->list->add_role($role_name);
		}
		$returned = $this->list->get_roles();
		$this->assertInternalType('array',$returned);
		return array('assigned'=>$assign, 'returned'=>$returned);
	}
	
	/**
	 * @depends test_get_roles
	 */
	function test_get_roles_length($roles) {
		$this->assertEquals(sizeof($roles['assigned']),sizeof($roles['returned']));
		return $roles;
	}
	
	/**
	 * @depends test_get_roles_length
	 */
	function test_get_roles_types($roles) {
		foreach ($roles['returned'] as $role) {
			$is_type = (get_class($role)==='WP_Role');
		}
		$this->assertTrue($is_type);
	}	
}