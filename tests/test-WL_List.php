<?php

class WL_List_Test extends WP_UnitTestCase {
	private $list;
	private $data;
	
	function __construct() {
		
		$this->data = New WL_Data();
		$this->data->initialize();
		$list_info = array(
			'id' => 1,
			'name' => 'cocoNOT',
			'time' => date("Y-m-d H:i:s")
		);
		
		$this->list = new WL_List($this->data,$list_info);
		
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
		return $user_id;
	}
	
	/**
	 * @depends test_remove_user
	 */
	function test_remove_user_param($user_id) {
		$users = $this->list->get_users();
		$this->assertFalse(in_array($user_id,$users));
	}
	
	function test_get_users() {
		$user_ids = $this->factory->user->create_many(5);
		$user_ids = $this->factory->user->create_many(3);
		foreach ($user_ids as $user_id) {
			WL_Dev::log("tgu: adding user ".$user_id.":");
			$this->list->add_user($user_id);
		}
		
		$returned = $this->list->get_users();
		$this->assertInternalType('array',$returned);
		return array('assigned'=>$user_ids,'returned'=>$returned);
	}
	
	/**
	 * @depends test_get_users
	 */
	function test_get_users_length($users) {
		WL_Dev::log($users);
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
	
	function test_rename_success() {
		$list = $this->data->create_whitelist('banana');
		$success = $list->rename('papaya');
		$this->assertTrue($success);
	}
	
	function test_rename_param() {
		$list = $this->data->create_whitelist('banana');
		$success = $list->rename('papaya');
		$this->assertEquals('papaya',$list->get_name());
	}
	
	function test_rename_db() {
		$list = $this->data->create_whitelist('banana');
		$success = $list->rename('papaya');
		$db_list = $this->data->get_whitelist_by('name','papaya'); 
		$this->assertEquals($list,$db_list);
	}
	
	function test_rename_failure() {
		$list1 = $this->data->create_whitelist('banana');
		$list2 = $this->data->create_whitelist('orange');
		$success = $list1->rename('orange');
		$this->assertFalse($success);
	}
	
	
	function test_add_page_success() {
		//$GLOBALS['silent'] = false;
		$page_id = $this->factory->post->create(array('post_type'=>'page'));
		$success = $this->list->add_page($page_id);
		$this->assertTrue($success);
		return $page_id;
	}
	
	/**
	 * @depends test_add_page_success
	 */
	function test_remove_page_success($page_id) {
		$GLOBALS['silent'] = false;
		$page_id = $this->factory->post->create(array('post_type'=>'page'));
		$success = $this->list->add_page($page_id);
		$this->assertTrue($success);
		$success = $this->list->remove_page($page_id);
		$this->assertTrue($success);
		$GLOBALS['silent'] = true;
	}
	
	//I should probably write more tests to see if it didn't get left in the param, or in the db... meeeeeh. Gonna live dangerously!
	
	function test_get_pages() {
		$page_ids = $this->factory->post->create_many(5,array('post_type'=>'page'));
		$page_ids = $this->factory->post->create_many(3,array('post_type'=>'page'));
		foreach ($page_ids as $page_id) {
			WL_Dev::log("adding page ".$page_id);
			$this->list->add_page($page_id);
		}
		
		$returned = $this->list->get_pages();
		$this->assertInternalType('array',$returned);
		return array('assigned'=>$page_ids,'returned'=>$returned);		
	}
	
	/**
	 * @depends test_get_pages
	 */
	function test_get_pages_length($pages) {
		$this->assertEquals(sizeof($pages['assigned']),sizeof($pages['returned']));
		return ($pages);		
	}
	
	/**
	 * @depends test_get_pages_length
	 */	
	function test_get_pages_type($pages) {
		$is_type = true;
		foreach ($pages['returned'] as $page_id) {
			$is_type = (is_numeric($page_id));
		}
		$this->assertTrue($is_type);
	}
	
	
	
}