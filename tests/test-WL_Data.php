<?php

class WL_Data_Test extends WP_UnitTestCase {
	private $data;
	
	function __construct() {
		$this->data = New WL_Data();
		$this->data->initialize();
	}
	
	
	function test_creating() {
		$result = $this->data->create_whitelist("mango");
		$this->assertTrue($result === 1);
	}
}
