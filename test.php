<?php
global $test_id;
$args = array(
	'A_function' => 'test_A_function',
	'B_function' => 'test_B_function'
);
$test = new AB_test( $args );
$test_id = $test->get_ABT_id();

function test_A_function() {
	add_filter( 'wp_get_nav_menu_items', 'leave_untouched', 10, 3 );
}
function test_B_function() {
	add_filter( 'wp_get_nav_menu_items', 'change_menu', 10, 3 );
}

function leave_untouched( $items, $menu, $args ) {
	return $items; //Current ID 58050, custom plans
}

/**
 * Filters the navigation menu items being returned.
 *
 * @param array  $items An array of menu item post objects.
 * @param object $menu  The menu object.
 * @param array  $args  An array of arguments used to retrieve menu item objects.
 */
function change_menu( $items, $menu, $args ) {
	$new_item = wp_setup_nav_menu_item( get_post( 22059 ) );
	
	foreach( $items as $key => &$value ) {
		if( $value->title == 'Custom plans' )
			$items[$key] = $new_item;
	}

	return $items;
}

function log_success( $download_id, $payment_id, $download_type ) {
	global $test_id;
	
	AB_log_success( $test_id );
}
add_action( 'edd_complete_download_purchase', 'log_success', 10, 3 );

/*function log_success() {
	global $test_id;
	
	if( strpos( $_SERVER['REQUEST_URI'], 'registrazione' ) !== false ) {
		AB_log_success( $test_id );
	}
}
add_action( 'init', 'log_success' );
*/
