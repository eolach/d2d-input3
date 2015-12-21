<?php
//  This file tests the output of the json chartdata
//  The file must be included with the other files to allow testing
/**
 *
 *
 * @author Neil
 * @version 0.5
 * @created 03-June-2015 10:06 AM
 * @updated 
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

require_once 'd2d-get-data.php';

if ( class_exists( 'D2D_fetch_data' ) ) {
	$D2D_fetch_data = new D2D_fetch_data();
}

echo 'starting ' . $D2D_fetch_data  -> test_get_data();