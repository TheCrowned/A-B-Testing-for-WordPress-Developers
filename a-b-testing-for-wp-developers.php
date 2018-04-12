<?php
/*
Plugin Name: A/B Testing for WP Developers
Plugin URI: http://www.thecrowned.org/
Description: Provides a simple A/B testing framework to WordPress developers.
Author: Stefano Ottolenghi
Version: 1.0
Author URI: http://www.thecrowned.org/
*/

/**
 * @author Stefano Ottolenghi
 * @copyright 2018
 */

global $ABTs;

function AB_log_success( $test_id ) {
	$counter = AB_maybe_init_option( $test_id );

	$counter[$test_id][$_COOKIE['AB_test_'.$test_id].'_success']++;

	$counter[$test_id][$_COOKIE['AB_test_'.$test_id].'_attempt']++; //treat it as a new customer

	update_option( 'AB_tests', $counter );
}

function AB_maybe_init_option( $test_id ) {
	$counter = get_option( 'AB_tests' );

	if( ! is_array( $counter ) OR ! isset( $counter[$test_id] ) ) {
		$counter[$test_id] = array(
			'A_attempt' => 0,
			'A_success' => 0,
			'B_attempt' => 0,
			'B_success' => 0
		);
	}

	return $counter;
}

function AB_init() {
	global $ABTs;

	foreach( $ABTs as $test_id => $test ) {
		if( ! isset( $_COOKIE['AB_test_'.$test_id] ) )
			$coin = mt_rand( 0, 1 );
		
		if( ! isset( $coin ) ) $A_or_B = $_COOKIE['AB_test_'.$test_id];
		else if( $coin == 0 ) $A_or_B = 'A';
		else $A_or_B = 'B';

		if( isset( $coin ) )
			$counter[$test_id][$A_or_B.'_attempt']++;

		$counter = AB_maybe_init_option( $test_id );
		
		update_option( 'AB_tests', $counter );
		
		setcookie( 'AB_test_'.$test_id, $A_or_B, time() + 7 * DAY_IN_SECONDS, '/' );
		$test[$A_or_B.'_function']();
	}
}
add_action( 'init', 'AB_init' );

class AB_test {

	public $ABT_id;

	public function __construct( $args ) {
		global $ABTs;
		
		$defaults = array( 'A_function' => '', 'B_function' => '' );	
		$args = wp_parse_args( $args, $defaults );

		if( ! is_callable( $args['A_function'] ) OR ! is_callable( $args['B_function'] ) )
			return new WP_Error( 'AB_invalid_callbacks', 'Must provide valid A/B callbacks.' );

		$key = md5( serialize( $args ) );
		$ABTs[$key] = $args;
		
		$this->ABT_id = $key;
	}

	public function get_ABT_id() {
		return $this->ABT_id;
	}

}

include( 'test.php' );
