<?php
/**
 * Plugin Name: D2D input
 * Plugin URI: http://www.healthengineer.com
 * Description: Manages input and display of D2D data.
 * 	Includes "Submit" functionality.
 * 	This version requires the MySQL indicators table later than 30 August 2015.
 * 	Corrected layout that interfered with new site footer
 * 	Upgraded to new AFHTO2015 theme
 * 	Eneabled quality_agree function, enabled share_agree, 
 * 	captured confirm review.
 * 	Upgraded to allow data input D2D 3.0
 * 	UPgeaded to allow data review D2D 3.0
 * Version: 2.1.0
 * Author: HealthEngineer
 * Author URI: http://www.HealthEngineer.com
 * Text Domain: NA
 * Domain Path: NA
 * Network: false
 * License: Licensed to AFHTO
 */


defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Define certain plugin variables as constants.
define( 'D2D_INPUT_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'D2D_INPUT__FILE__', __FILE__ );
define( 'D2D_INPUT_BASENAME', plugin_basename( D2D_INPUT__FILE__ ) );
// Define certain plugin variables as constants.
define( 'D2D_REVIEW_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'D2D_REVIEW__FILE__', __FILE__ );
define( 'D2D_REVIEW_BASENAME', plugin_basename( D2D_REVIEW__FILE__ ) );


require_once D2D_INPUT_ABSPATH . 'includes/d2d-input-class.php';
require_once D2D_INPUT_ABSPATH . 'includes/d2d-indicator-specs.php';
require_once D2D_INPUT_ABSPATH . 'includes/d2d-indicator.php';
// require_once D2D_INPUT_ABSPATH . 'd2d-review-addon.php';
require_once D2D_REVIEW_ABSPATH . 'includes/d2d-review-class.php';
require_once D2D_REVIEW_ABSPATH . 'includes/d2d-get-data.php';

// function d2d_load_styles2() {
// }

//add_action( 'wp_enqueue_scripts', 'd2d_load_styles2' );
add_action('admin_menu', 'd2d_plugin_menu');
add_action('wp_enqueue_scripts', 'd2d_load_scripts');

function d2d_plugin_menu(){ 
	add_menu_page('D2D Indicator Fields', 'D2D inputs', 'manage_options', 'd2d-plugin-menu', 'd2d_plugin_options');
	// add_action( 'admin_init', 'register_mysettings' ); 
}

function d2d_plugin_options(){ include('includes/d2d-plugin-admin.php');}



function d2d_load_scripts( $hook ) {
	// if( $hook !)
    wp_enqueue_style( 'd2d-styles',  plugins_url( 'd2d-styles.css', D2D_INPUT__FILE__  ) );
	wp_enqueue_style('d2d-review-styles', plugin_dir_url( __FILE__) . 'css/d2d-review.css'  );
	// wp_enqueue_script( 'd2d_js', plugin_dir_url( __FILE__) . 'js/d2d_js.js' , array( 'jquery' ), '1.0' );
// enqueue and localise scripts
wp_enqueue_script( 'my-ajax-handle', plugin_dir_url( __FILE__ ) . 'js/d2d_js.js', array( 'jquery' ) );
wp_localize_script( 'my-ajax-handle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}



if ( class_exists( 'D2D_fetch_data' ) ) {
	$D2D_fetch_data = new D2D_fetch_data();
}


// THE FUNCTION
function d2d_get_data() {
	global $D2D_fetch_data;
	/* this area is very simple but being serverside it affords the possibility of 
	retreiving data from the server and passing it back to the javascript function */
	$d2d_data = $_POST;
	header( "Content-Type: application/json" );
    // $D2D_fetch_data -> process_data( $d2d_data );
	echo $D2D_fetch_data -> process_data( $d2d_data );// this is passed back to the javascript function
	die();// wordpress may print out a spurious zero without this - can be particularly bad if using json
}
// THE AJAX ADD ACTIONS
add_action( 'wp_ajax_get_new_data', 'd2d_get_data' );
add_action( 'wp_ajax_nopriv_get_new_data', 'd2d_get_data' ); // need this to serve non logged in users