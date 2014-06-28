<?php

class WL_Data_Test extends WP_UnitTestCase {
	private $data;
	
	function __construct() {
		$this->data = New WL_Data();
		$this->data->initialize();
	}
	
	
	function test_creating() {
		$result = $this->data->create_whitelist("mango");
		$this->assertEquals('exists',$result[0]); //this seems to create new whitelist with an incremented id? but the whitelist isn't in the database? whu???
	}
	
	
}
