<?php
namespace MobilizeAmericaTests;
require_once('MA_WPUnitTestCase.php');
require_once('Test_Options.php');

class Tests_Events extends MA_WPUnitTestCase {

	public function tearDown() {
		parent::tearDown();
	}

	public function test_get_events() {

        parent::setUp();
                        
        $this->assertTrue(class_exists('\MZ_Mobilize_America\Core\Plugin_Core'));
        
	}

}