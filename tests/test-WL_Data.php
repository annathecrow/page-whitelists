<?php

class WL_Data_Test extends WP_UnitTestCase {
	private $data;
	
	function __construct() {
		$this->data = New WL_Data();
		$this->data->initialize();
		//$this->dummy_user = new WP_User(1);
	}
	
	
	function test_create_whitelist_single() {
		$result = $this->data->create_whitelist("mango");
		//$this->assertEquals('created',$result[0]);
		$this->assertInstanceOf('WL_List',$result);
	}
	
	function test_create_whitelist_multiples() {
			$this->data->create_whitelist("mango"); //mango once
			$result = $this->data->create_whitelist("mango"); //mango twice
			$this->assertTrue($result); //no two mangoes!
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
}
