<?php

class SampleTest extends WP_UnitTestCase {
	
	private $plugin;
	
	function setUp() {
		$this->plugin = new Whitelists();
	}

	function test_get_whitelist() {
		$groups = array('dummy');	
		$list = $this->plugin->get_whitelist($groups);
		$this->assertEquals(array(291,289),$this->plugin->get_whitelist($groups));
		//$this->assertEquals(2,sizeof($list));
	}
	
	function test_get_assigned_groups() {
		$user = 3;
		$this->assertEquals(array('dummy'),$this->plugin->get_assigned_groups($user));
	}
}

