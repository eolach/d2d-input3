<?php

/**
 * basic indicator
 *
 * @author Neil
 * @version 0.5
 * @created 17-Apr-2015 8:52:14 PM
 * @updated 18-Apr-2015
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class D2D_indicator {

	public $indicator_class;
	public $full_label;
	public $short_label;
	public $value = '';
	public $css_style;
	public $indicator_group;
	public $valid;
	public $errors = array();
	public $m_Database_record;

	function __construct( $params ) {
		$this -> indicator_class = $params['indicator_class'];
		$this -> full_label = $params['full_label'];
		$this -> short_label = $params['short_label'];
		$this -> indicator_group = $params['indicator_group'];
		$this -> menu_class = $params['menu_class'];
	}

	function __destruct() {
	}

	public function set_value( $val ) {
		$this -> value = $val;
	}

	public function get_value( $val ) {
		return $this -> value;
	}

	public function get_name() {
		return $this -> full_label;
	}

	public function retrieve_from_db( $db_result ) {
		$this -> value = $db_result[ $this -> short_label ];
	}

	public function save_to_db( $post_v) {
		$value = $post_v[ $this -> short_label ];
		// echo 'Saving 0' . $value ;
		// $entry = $value; ? ($this -> short_label .' = "' . $post_v[ $this -> short_label ] . '" ') : NULL;
		$entry = $this -> short_label .' = "' . $post_v[ $this -> short_label ] . '" ';
		return $entry;
	}

	public function display_in_form() {
		$echo_out = '<tr><td>' . $this -> full_label . '</td><td>
		<input type="text" value="' . $this -> value . '" name = "' .  $this -> short_label . '" size="6" /></td></tr>';
		echo $echo_out;
	}

	public function retrieve_indicator_from_form( $post_result ) {
		$temp_value = $post_result[ $this -> short_label];
		// if ($this -> short_label == 'cost'){
			// $temp_value = $temp_value . 'asd';
		// }
		$this -> value = $temp_value;
	}

	/**
	 * Validates a single entry, and does the
	 *  actual numeric/positive thing.
	 * @param  String $value values being tested
	 * @return Array with revised value if there was and error
	 * a flag indicating if the value was valid
	 * and a message if it was not.
	 */
	public function validate_single_value( $value ){
		$temp_val = preg_replace('/[\$,%\^#\*\?\+\/&@]/', '', $value);
		if ( $temp_val < 0 ) {
			$temp_val *= -1;
		}
		$valid = "valid";
		$err_array = array();
		$err_array['value'] = $temp_val;
		$err_array['valid'] = "valid";
		if  ( ( $value != NULL ) and
			( ( ! is_numeric( $temp_val ) )
				or ( $value < 0 ) )  ) 
		{
			if ( preg_match("/error/", $value ) == 0 ){
				$value =  'error';
			}
			$valid = "invalid";
			$err_array = array(
				"value" => $temp_val,
				"valid" => $valid,
				"message" =>  '<em>' . $this -> full_label . ': '. $temp_val . '</em> must be numeric and positive.'
			);
		} 
		return $err_array;
	}

	/**
	 * Validate tests to that values are numeric and positive
	 * @return Array where "valid" holds either the work "valid" or "invalid",
	 * and 'message' holds an error message if there is one.
	 */
	public function validate() {

		$ret_value = array();
		$ret_value = $this -> validate_single_value( $this -> value );
		$this -> value = $ret_value['value'];
		$this -> errors = array(
			"valid" => $ret_value['valid'],
			"message" =>  $ret_value['message']
			);
		return $this -> errors;
	}

	
	public function format_decimals( $number0 ) {
		$number = ($number0 == "") ? NULL : $number;
		if ( !is_null( $number )  or ($number != "" ) ){
			return number_format( $number, 3 );
		} else {
			return NULL;
		}
	}

	public function replace_value(&$target, $source){
		$target = $source;
	}
}

class d2d_simple_indicator extends D2D_indicator {
	
	public function set_value( $val ) {
		$temp_val = preg_replace('/[\$,%]/', '', $val);
		$this -> value = $temp_val;
	}

	public function retrieve_from_db( $db_result ) {
		$this -> set_value( $db_result[ $this -> short_label ] );
	}

	public function retrieve_indicator_from_form( $post_result ) {
		$this -> set_value( $post_result[ $this -> short_label] );
	}

	public function save_to_db( $post_v) {
	if ($post_v[ 'rates_quality_flag'] == 'rate' ){

	$value = preg_replace('/[\$,]/', '', $post_v[ $this -> short_label ]);
	// $entry = $value ? ($this -> short_label .' = "' . $value . '" ') : NULL;
	if ( $value != NULL){
			$entry = $this -> short_label .' = "' . $value . '" ';
		} else {
			$entry = $this -> short_label .' = NULL ';

		}
	} else {
		$entry = NULL;
	}
	return $entry;
	}


}


class d2d_team_indicator extends D2D_indicator {

	function __construct( $params ) {
		parent::__construct( $params );
		$this -> value = $params['value'];
	}

	public function display_in_form() {
		$echo_out = $this -> value;
		echo $echo_out;
	}


	// public function retrieve_from_db( $db ){

	// }

	public function validate( $code, $new_code, $latest_requested = NULL ) {
		if ( $code == 'Enter code' ) { // Didn't change the default code - reject
			$this -> valid = false;
			$this -> errors = array(
				"valid" => "invalid enter",
				"message" => 'You must enter a valid code. <br />
				Re-type your code and press "Load new data".'
			);
		} elseif ( ( $code == NULL ) or ( $code == '' ) ) { // Nothing in the code text box - reject
			$this -> valid = "invalid no code";
			$this -> errors = array(
				"valid" => "invalid_no_code",
				"message" => 'You must enter a valid code. <br />
				Re-type your code and press "Load new data".'
			);
		} elseif ( ( $code != $new_code ) ) { // the code could not be found
			// There is no entry in the database for the combination of team code and year code.
			// You will ve asked if you want to register a new record for this team and year.

			if ($latest_requested == 2 ){
				$this -> valid = "invalid new year";
				$this -> errors = array(
					"valid" => "valid_new_code",
					"message" => 'The database does not have any record for team "<e>' . $code . '"</em>.<br />
					If you have made an error, re-enter the team code and click "Load new data" again.<br />
					If you wish to add a new record for this team for the current iteration, click on "Register".');
			} else {
				$this -> valid = "invalid wrong code";
				$this -> errors = array(
					"valid" => "invalid_no_code",
					"message" => 'The database does not have any record for team "<e>' . $code . '"</em>.<br />
					If you have made an error, or if you wish to add a new record for this team for the current iteration,
					re-enter the team code and click "Load new data" again.');
			}
		} else {

			if ( $latest_requested ==1 ) {
			// There is no entry in the database for the combination of team code and year code.
			// You will ve asked if you want to register a new record for this team and year.
			$this -> valid = "invalid wrong year";
			$this -> errors = array(
				"valid" => "invalid_wrong_year",
				"message" => 'The database does not have a record for team "<e>' . $code . '"</em>, for the selected iteration.<br />
				If you have made an error, re-enter a team code, select another iteration and click "Load new data" again.');
			} elseif ( $latest_requested == 2 ) {
				$this -> valid = "valid code new year";
				$this -> errors = array(
					"valid" => "valid_new_year",
					"message" => 'The database has an earlier record for team "<e>' . $code . '"</em>.<br />
					If you wish to add a new record for this team for the current iteration, click on "Register".');
			} else{
				$this -> valid = "valid";
				$this -> errors = array(
					"valid" => "valid"
				);
			}

		}
		return $this -> errors;
	}

	public function save_to_db() {
		$value = $post_v[ $this -> short_label ];
		$entry = $value ? ($this -> short_label . ' =  "'. $value . '"') : NULL;
		return $entry;
	}

	public function retrieve_indicator_from_form( $post_result ) {
	}
}

/**
 *
 */
class D2D_select_indicator extends D2D_indicator{

	public $selections = array();
	public $selected;
	public $default;
	
	function __construct( $params ) {
		parent::__construct( $params );
		$this -> selections = $params['value'];
		$this -> options = $params['options'];
		$this -> selected = $this -> options[0];
	}

	// Manage <select> tags
	private function set_select_tag( $opt_value, $tag_value ) {
		if ( $tag_value == $opt_value ) {
			$return_txt = 'selected="selected"';
		} else {
			$return_txt = ''; //set_select_tag(1);
		}
		return $return_txt;
	}

	public function set_value( $val ){
		$this -> selected = $val;
	}
	
	public function retrieve_indicator_from_form( $post_result ) {
		$this -> selected = $post_result[ $this -> short_label];
	}


	public function retrieve_from_db( $db_result ) {
		$this -> selected = $db_result[ $this -> short_label ];
	}

	public function display_in_form() {
		$opts = $this -> options;
		$names = $this -> selections;
		$keys = array_keys($names);
		foreach ( $keys as $k ) {
			if ( $opts[$k] == $this -> selected ) {
				$slctd = ' selected = "selected"';
				$slctd_name = $names[$k];
			} else {
				$slctd = '';
			}
			$echo_out .= '<option value= "' . $opts[$k] . '"  '  . $slctd . '>' . $names[$k] . '</option>';
		}
		echo $echo_out;
	}

	public function save_to_db( $post_v) {
		$temp_out = $this -> short_label . ' = "' . $post_v[ $this -> short_label  ] . '"';
		return $temp_out;
	}
}

class D2D_rate_indicator extends D2D_indicator{

	public $numerator;
	public $denominator;
	public $rate;

	function __construct( $params ){
		parent::__construct($params);
		$this -> set_value( $this -> value);
	}

	public function set_value( $val ) {
		$this -> numerator = $val[0];
		$this -> denominator = $val[1];
		$this -> rate = $val[2];
	}
	
	public function reset_value( $val ) {
		$keys = array_keys($val);
		$this -> numerator = $val[$keys[0]];
		$this -> denominator = $val[$keys[1]];
		$this -> rate = $val[$keys[2]];
	}

	public function get_value( $val ) {
		$value = array(
			$this -> numerator,
			$this -> denominator,
			$this -> rate
		);
		return $this -> value;
	}


	public function retrieve_from_db( $db_result ) {
		// echo 'Retrieving from ' .  $this -> full_label . '<br/>';
		$this -> numerator = $db_result[ $this -> short_label . '_N'  ];
		$this -> denominator = $db_result[ $this -> short_label . '_D'  ];
		$this -> rate = parent::format_decimals( $db_result[ $this -> short_label   ] );
	}

	public function save_to_db( $post_v ) {
		$entry = $this -> short_label . '_N' .' = "'  . $post_v[ $this -> short_label . '_N' ] . '", ';
		$entry .= $this -> short_label . '_D' . '= "'  . $post_v[ $this -> short_label . '_D' ] . '", ';
		$entry .= $this -> short_label . '= "'  . $post_v[ $this -> short_label  ] . '" ';
		return $entry;
	}

	public function display_in_form() {
		$echo_out = '<tr><td class="d2dinputcoldata">' . $this -> full_label . '</td>
			<td class="d2dinputcoldata"><input type="text" value="' . $this -> numerator . '" name="' . $this -> short_label . '_N" size="6" /></td>
			<td class="d2dinputcoldata"><input type="text" value="' . $this -> denominator . '" name="' . $this -> short_label . '_D" size="6" /></td>
			<td class="d2dinputcoldata"><input type="text" value="' .  $this -> rate  . '" name="' . $this -> short_label . '" size="6" /></td></tr>';
		echo $echo_out;
	}

	public function retrieve_indicator_from_form( $post_result ) {
		$this -> numerator = $post_result[ $this -> short_label . '_N'];
		$this -> denominator = $post_result[ $this -> short_label . '_D'];
		$this -> rate = $post_result[ $this -> short_label ];
	}

	/**
	 * Tests all three values for positive/numeric
	 * and also tests the calculations for the triplet.
	 * @return [type] [description]
	 */
	public function validate() {
		$values;
		$ret_value = array();

		$this -> errors = array(
			"valid" => "valid",
			"message" =>  NULL
			);	

		$temp_vars = array();
		$temp_vars[$this -> short_label . '_N'] = $this -> numerator;
		$temp_vars[$this -> short_label . '_D'] = $this -> denominator;
		$temp_vars[$this -> short_label ] 		= $this -> rate;
		$keys = array_keys( $temp_vars );
		$this -> valid = true;
		
		// Test each of the values
		foreach ( $keys as $k ) {
			$ret_value = parent::validate_single_value( $temp_vars[$k] );
			$temp_vars[$k] = $ret_value['value'];
			if ($ret_value['valid'] == "invalid"){
				$this -> errors [ 'valid' ] = "invalid";
				$this -> errors ['message' ]=  '<em>All entered values in ' . $this -> full_label . '</em> must be numeric and non-negative.';
			}		
		}
		$this -> reset_value($temp_vars);
		// var_dump($values);

		// Check for denominator == 0;
		if ($this -> denominator == 0){
			//$this -> rate = NULL;
			$this -> denominator = NULL;
		}

		// Check that there is a ratio to compute
		if ( ( $this -> numerator != NULL ) 
			and (( $this -> denominator != NULL ) 
				AND ( $this -> denominator > 0.01 ) ) ) {
			// If no rate is specified
			if ( $this -> rate == NULL ) {
				$this_rate = ( $this -> numerator / $this -> denominator ) *100;
				$this -> rate = number_format( $this_rate, 1 );
			} else {
			// if there is a rate there already and the denominator is not zero
			// calculate a provisional ratio
				if ( $this -> denominator > 0.01){
					$ratio = $this -> numerator / $this -> denominator;
					// If the reported rate is probably not a percentage, multiply it by 100
					if ( ($this -> rate > 0.0) and ($this -> rate < 1.0) ){
						$this -> rate = $this -> rate * 100;
					}
					// If the numerator is greater than the denominator
					if (  ( $this -> numerator > $this -> denominator) 
						or ( ( ( $ratio *100 )  ) > 0.01 ) ){
						$this -> rate = number_format($ratio * 100, 1 );
						$this -> errors [ 'valid' ] = "invalid";
						$this -> errors ['message' ] .=  '<br />
						Rate has been recalculated for <em>' . $this -> full_label . '</em>.';
					}
				}
			}
		} else {
			if ( $this -> rate > 0.0 ){
				$this -> rate = number_format( $this -> rate, 1 );
			}
		}
		return $this -> errors;
	}
}

class D2D_rostered_rate_indicator extends D2D_indicator{

	public $total_inds;
	public $rostered_inds;

	function __construct( $params ) {
		parent::__construct( $params );
		$this -> total_inds = new D2D_rate_indicator( $params );
		$this -> rostered_inds = new D2D_rate_indicator( $params );
		$this -> total_inds -> short_label = 'total_' . $this -> total_inds -> short_label;
		$this -> rostered_inds -> short_label = 'rostd_' . $this -> rostered_inds -> short_label;
	}

	public function validate(){
		$this -> valid = "valid";
		$this -> errors = array(
			"valid" => '"valid"',
			"message" => "Not really valid"
			);
		$total_errors = $this -> total_inds -> validate();
		$rostered_errors = $this -> rostered_inds -> validate();
		$comp_message = '';
		if ($this -> $total_errors['valid'] == "invalid"){
			$comp_message .= $total_errors['message'] . '<br />';
			$this -> valid = "invalid";
		}
		if ($this -> $rostered_errors['valid'] == "invalid"){
			$comp_message .= $rostered_errors['message' ]. '<br />';
			$this -> valid = "invalid";
		}
		
		if ( $this -> valid  == "invalid"){
			$this -> errors = array(
				"valid" => "invalid",
				"message" => $comp_message
				);
		} else {
			$this -> errors = NULL;
		}
		return $this -> errors;
	}

	public function retrieve_from_db( $db_result ) {
		$temp_string = $db_result[ $this -> short_label];
		if ($temp_string == NULL){
			$temp_string = "_____";
		}
		$temp_array = explode("_", $temp_string);
		$this -> total_inds -> numerator = $temp_array[0];
		$this -> total_inds -> denominator = $temp_array[1];
		// $this -> total_inds -> rate = parent::format_decimals( $temp_array[2] );
		$this -> total_inds -> rate =  $temp_array[2] ;
		$this -> rostered_inds -> numerator = $temp_array[3];
		$this -> rostered_inds -> denominator = $temp_array[4];
		// $this -> rostered_inds -> rate = parent::format_decimals( $$temp_array[5] );
		$this -> rostered_inds -> rate =  $temp_array[5] ;
	}

	public function save_to_db_old( $post_v ) {
		$entry = $this -> short_label . '_N' .' = "'  . $post_v[ $this -> short_label . '_N' ] . '", ';
		$entry .= $this -> short_label . '_D' . '= "'  . $post_v[ $this -> short_label . '_D' ] . '", ';
		$entry .= $this -> short_label . '= "'  . $post_v[ $this -> short_label ] . '", ';
		$entry .= 'rostd_' . $this -> short_label . '_N' .' = "'  . $post_v[ 'rostd_' . $this -> short_label . '_N' ] . '", ';
		$entry .= 'rostd_' . $this -> short_label . '_D' . '= "'  . $post_v[ 'rostd_' . $this -> short_label . '_D' ] . '", ';
		$entry .= 'rostd_' . $this -> short_label . '= "'  . $post_v[ 'rostd_' . $this -> short_label ] . '" ';
		return $entry;
	}
	public function save_to_db( $post_v ) {
		// Save to database only if there is data in the form
		if ($post_v[ 'rates_quality_flag'] == $this -> indicator_group ){
		$entry = $post_v[ $this -> short_label . '_N' ] . '_';
		$entry .= $post_v[ $this -> short_label . '_D' ] . '_';
		$entry .= $post_v[ $this -> short_label ] . '_';
		$entry .= $post_v[ 'rostd_' . $this -> short_label . '_N' ] . '_';
		$entry .= $post_v[ 'rostd_' . $this -> short_label . '_D' ] . '_';
		$entry .= $post_v[ 'rostd_' . $this -> short_label ];
		$entry =  $this -> short_label . ' =  "' . $entry . '"' ;
		} else {
			$entry = ''; //$post_v[ 'rates_quality_flag'] . ' ' . $this -> indicator_group;
		}
		return $entry;
	}

	public function retrieve_indicator_from_form( $post_result ) {
		$this -> rostered_inds -> numerator = $post_result[ 'rostd_' . $this -> short_label . '_N'];
		$this -> rostered_inds -> denominator = $post_result[ 'rostd_' . $this -> short_label . '_D'];
		$this -> rostered_inds -> rate = $post_result['rostd_' .  $this -> short_label ];
		$this -> total_inds -> numerator = $post_result[ $this -> short_label . '_N'];
		$this -> total_inds -> denominator = $post_result[ $this -> short_label . '_D'];
		$this -> total_inds -> rate = $post_result[ $this -> short_label ];
	}


	public function display_in_form() {
		if ( ( $this -> menu_class != "ICES") AND (  $this -> short_label != "child_imm") ) {
			$echo_out = '<tr><td style="width: 200px">' . $this -> full_label . '</td>
			<td style="padding: 0px;"><input type="text" value="' . $this -> rostered_inds -> numerator . '" name="rostd_' . $this -> short_label . '_N" size="6" /></td>
			<td style="padding: 0px;"><input type="text" value="' . $this -> rostered_inds -> denominator . '" name="rostd_' . $this -> short_label . '_D" size="6" /></td>
			<td style="padding: 0px; border-right: 2px solid #000;"><input type="text" value="' . $this -> rostered_inds -> rate . '" name="rostd_' . $this -> short_label . '" size="6" /></td>';
		} else {
			$echo_out = '<tr><td style="width: 200px">' . $this -> full_label . '</td>
			<td style="padding: 0px;"></td>
			<td style="padding: 0px;"></td>
			<td style="padding: 0px; border-right: 2px solid #000;"></td>';
		}
		
		$echo_out .= '
			<td style="padding: 0px;"><input type="text" value="' . $this -> total_inds -> numerator . '" name= "' . $this -> short_label . '_N" size="6" /></td>
			<td style="padding: 0px;"><input type="text" value="' . $this -> total_inds -> denominator . '" name="' . $this -> short_label . '_D" size="6" /></td>
			<td style="padding: 0px;"><input type="text" value="' . $this -> total_inds -> rate . '" name="' . $this -> short_label . '" size="6" /></td></tr>';
		echo $echo_out;
	}
}

class D2D_text_indicator extends D2D_indicator{

	public $text;

	function __construct( $params ){
		parent::__construct( $params );	
		$this -> text = $params['text'];
	}

	public function retrieve_indicator_from_form( $post_result ) {
		$this -> text = $post_result[ $this -> short_label];
	}

	public function save_to_db( $post_v ){
		$value = $post_v[ $this -> short_label ];
		$entry = $value ? ($this -> short_label .' = "' . $value . '" ') : NULL;

		return $entry;
	}

	public function retrieve_from_db( $db_result ) {
		// $this -> value = $db_result[ $this -> short_label ];
		$this -> text = $db_result[ $this -> short_label ];
	}

	public function display_in_form() {

	}
}




