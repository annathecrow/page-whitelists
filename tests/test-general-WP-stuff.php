<?php

class General_Test extends WP_UnitTestCase {
	
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
	
	function test_get_roles() {
		$roles_array = get_editable_roles();
		foreach ($roles_array as $role_array) {
			$role_object = get_role(strtolower($role_array['name']));
			$this->assertInstanceOf('WP_Role',$role_object);
			//$this->assertEquals()
		}
	}
}