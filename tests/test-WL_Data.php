<?php

class WL_Data_Test extends WP_UnitTestCase {
	private $data;
	
	function __construct() {
		
		
		$this->data = New WL_Data();
		$this->data->initialize();
	}
	
	function test_create_whitelist_single() {
		$result = $this->data->create_whitelist("mango");	
		$this->assertInstanceOf('WL_List',$result);
	}
	
	function test_create_whitelist_multiples() {
		$GLOBALS['silent'] = false;
		$this->data->create_whitelist("mango"); //mango once
		$result = $this->data->create_whitelist("mango"); //mango twice
		$this->assertTrue($result); //no two mangoes!
		$GLOBALS['silent'] = true;
	}
	
	function test_get_whitelists_count() {
		$this->data->create_whitelist("mango");
		$this->data->create_whitelist("apple");
		$array = $this->data->get_whitelists();
		$this->assertCount(2,$array);
	}
	
	function test_get_whitelists_type() {
		$this->data->create_whitelist("mango");
		$this->data->create_whitelist("apple");
		$array = $this->data->get_whitelists();
		foreach ($array as $list) {
			$this->assertInstanceOf('WL_List',$list); //this will try every member of $array and fail on first that doesn't pass
		}		
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
		$this->assertTrue(user_can($user_id,'edit_whitelist_'.$result->get_id()));
	}
	
	function test_remove_whitelist_user() {
		$result = $this->data->create_whitelist("mango");
		$user_id = $this->factory->user->create();
		$this->data->add_whitelist_user($result->get_id(),$user_id);
		$this->assertTrue(user_can($user_id,'edit_whitelist_'.$result->get_id()));
		$this->data->remove_whitelist_user($result->get_id(),$user_id);
		$this->assertFalse(user_can($user_id,'edit_whitelist_'.$result->get_id()));
	}
}
