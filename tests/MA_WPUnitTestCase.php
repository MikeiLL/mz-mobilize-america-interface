<?php
require_once('Test_Options.php');

/**
 * Class MA_WPUnitTestCase
 *
 * @package MA_WPUnitTestCase
 */

/**
 * Add a logging method to WP UnitTestCase Class.
 */
abstract class MA_WPUnitTestCase extends \WP_UnitTestCase {

	public function el($message){
		file_put_contents('./log_'.date("j.n.Y").'.log', $message, FILE_APPEND);
	}
	
	public static function setUpBeforeClass(){
		//global vars setup
		$basic_options_set = array(
			'organization_id' => Test_Options::$_ORGANIZATION_ID
		);
	
		add_option( 'mz_mobilize_america', $basic_options_set, '', 'yes' );
	
		update_option('mz_mobilize_america', [
			'organization_id' => Test_Options::$_ORGANIZATION_ID
		]);
	}
}