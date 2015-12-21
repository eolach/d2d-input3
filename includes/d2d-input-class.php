<?php
/**
 *
 *
 * @author Neil
 * @version 0.9
 * @created 17-Apr-2015 8:54:10 PM
 * @updated 2015-07-20 
 *
 * Description:
 * This class manages the input of data. 
 * It is created when the short code "[input data]" is first encountered
 * The forms classes and the indicators classes depend on calls from this class
 *
 * The class includes declarations followed by a main loop that preloads all the indicators and forms
 * and then responds to "submit" calls to the server.
 * Each call carries out a series of checks (to see which data is coming back from the server)
 * and then manages the updating of the forms
 * and either retrieving data from the database if the form has changed,
 * or refreshing the database if there is new data in the existing form.
 *
 * The main loop includes branches that handle saving of data to the database for future review
 * and submission of final data when the data input is complete and verified. 
 *
 * Data input is confined to D2D iteration 2.0. When the next iteration is ready for input, the
 * code will need to be updated to allow entry, review and submission for that iteration, while preventing revision of earlier data
 *
 * The forms are populated by specific subsets of the indicators (detailed in the notes to the forms class)
 * and the indicators are created based on specifications set out in the indicator specification class.
 * 
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

require_once D2D_INPUT_ABSPATH . 'includes/d2d-form.php';

if ( !class_exists( 'D2D_input' ) ) {
	class D2D_input {

	// DECLARATIONS
		// forms
		public $team_form;
		public $data_form;
		public $base_form;
		public $quality_form;
		public $new_team_form;
		public $control_form;
		public $submit_form;
		public $confirm_form;

		public $msg;
		public $error_msg = array();
		public $indicator_specs;
		public $indicator_set;

		public $quality_agree = NULL;

		// Tracking variables
		public $id = NULL;
		public $team_code = 'Enter code"';
		public $my_year = 2000;
		public $old_team_code = NULL;
		public $old_year_code;
		public $latest_year_code = NULL;
		public $latest_year_requested = 0; // 0 for exising record, 1 for old record, 2 for current record
		// Current variables
		public $db_results = array(); // Entire response from database (all years)
		public $db_record = array(); // Response from database for one year
		public $db_team = array(); // Response from database for first available year
		public $post_values; // Response from form

		// Buttons
		public $submit_button;
		public $rates_button;
		public $quality_button = false;
		public $register_button;
		public $cancel_button;
		public $send_data_button;

		// Flags
		public $send_data_flag = false;
		public $register_flag = false;
		public $confirm_submit_flag = false;
		public $same_team = false;
		public $same_year = false;
		public $rates_quality_flag = 'rates';
		public $toggle_rates_quality = false;
		public $submit_flag = false;
		public $cancel_flag = false;
		public $save_status = 'saved';

		//  Control flags
		public $short_form = true; // True when asking for new record
		public $first_time = true; // True before first request is sent
		public $valid_team = 0; // 1 if team and period found, 2 if new record is allowed
		public $valid_data = false; //
		public $new_record = true; // True if user has asked for new record



		public function __construct() {
			add_shortcode( 'input data', array( $this, 'data_shortcode' ) );
		}

		//////////////////////////////////////////
	// MAIN LOOP
		/**
		 * Main loop
		 *
		 * @return [type] [description]
		 */
		public function process_functions() {
			$this -> pre_load();
			//  Continually respond to POSTS from the server
			//  This is the main loop of the input process
			$this -> respond_to_POSTS();
		}


		/////////////////////////////////////////
	// PRELOAD
		/**
		 * Prepare the indicators and load the forms
		 *
		 * @return [type] [description]
		 */
		public function pre_load() {

			global $d2d_data_specs;
			/*********************************/
			// Loading completge set of indicator objects
			$this -> indicator_set = $d2d_data_specs -> make_indicators();

			// Saves the last option in year code selector drop-down on the input page
			$this -> latest_year_code = $d2d_data_specs -> get_current_period();
			// echo "Current period" . $this -> latest_year_code;

			// Loading complete set of forms
			$this -> load_forms();
		} 
		/**
		 * Creates and loads the forms ready for display when needed
		 *
		 * @return nothing
		 */
		protected function load_forms() {
			$this -> q_button_form = new D2D_quality_summary_form( $this -> indicator_set, $this, 'qual_agree' );
			$this -> team_form = new D2D_team_form( $this -> indicator_set, $this, 'header' );
			$this -> profile_form = new D2D_profile_form( $this -> indicator_set, $this, 'profile' );
			$this -> data_form = new D2D_data_form( $this -> indicator_set, $this, 'data' );
			$this -> base_form = new D2D_rostered_rate_form( $this -> indicator_set, $this, 'rate' );
			$this -> quality_form = new D2D_rostered_rate_form( $this -> indicator_set, $this , 'quality');
			// $this -> control_form = new D2D_control_form( $this -> indicator_set, $this );
			$this -> new_team_form = new D2D_new_team_form( $this -> indicator_set, $this );
			$this -> submit_form = new D2D_submit_form( $this -> indicator_set, $this );
			$this -> confirm_form = new D2D_confirm_form( $this -> indicator_set, $this );
		}

	// CONTINUALLY RESPOND TO POSTS FROM SERVER
		/**
		 * respond to server requests
		 * @return [type] [description]
		 */
		protected function respond_to_POSTS() {
			// On all but the first pass, gets the post_values from the most recent form
			if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
				$this -> post_values = $_POST;
				// If this is the first session set the flag for future ones
				$this -> first_time = false;
				// Input process starts with a short form asking for the team code
				// This flag is used to confirm an existing team, or to offer
				// registration for a new team.
				// If last form was a short form, save flag
				$this -> short_form = $this -> post_values['short_form'];
				
				$this -> process_post_values();
			}
			$this -> render_forms();
			 
		}


		//////////////////////////////////////////////////
	// PROCESS POST VALUES
		//
		/**
		 * Reads post data and sets various flags to guide later handling of data
		 * @return [type] [description]
		 */
		public function process_post_values(){
				// Set the relevant flags:
				if ( $this -> post_values['form_submitted'] == 'Submit final data' ) {
					$this -> submit_flag = true;
				}

				if ( $this -> post_values['register'] == 'Register' ) {
					$this -> register_flag = true;
				}

				//  Manage the toggle between the rates and the quality pages
				if  ( ( isset ($this -> post_values['rate'] )  )
					and ( $this -> post_values['rates_quality_flag'] == 'quality') ) { 
					$this -> toggle_rates_quality = true;
					$this -> rates_quality_flag = 'rate';
				} elseif ( ( isset ($this -> post_values['quality'] )  )
					and ( $this -> post_values['rates_quality_flag'] == 'rates') ){
					$this -> toggle_rates_quality = true;
					$this -> rates_quality_flag = 'quality';
				} else {
					$this -> toggle_rates_quality = false;
					$this -> rates_quality_flag = $this -> post_values[ 'rates_quality_flag'];
				}
				
				if ( isset ( $this -> post_values['quality'] )  ){
					$this -> rates_quality_flag = 'quality';
				}
				if ( isset ( $this -> post_values['rate'] )  ){
					$this -> rates_quality_flag = 'rate';
				}

				// Manage the special buttons
				if ( $this -> post_values['confirm_submit'] == 'Confirm' ) {
					$this -> confirm_submit_flag = true;
				}

				if ( $this -> post_values['cancel_submission'] == 'Cancel' ) {
					$this -> cancel_flag = true;
				}

				if ($this -> post_values['send_data'] == 'Send data' ) {
					if ( $this -> post_values['confirm_review'] == 'Yes' ){
						$this -> msg = 'Data has been submitted. <br />';
						$this -> send_data_flag = true;
						$this -> save_status  = 'locked';
					} else {
						$this -> msg = 'Data has not yet been submitted. <br />
						You must confirm that your team has gone through the necessary team-based approval process.<br />';
					}
				} else {
					$this -> save_status  = $this -> post_values['save_status'];
				}
				

				// $this -> quality_agree  = isset($this -> post_values['quality_agree']) ? "Yes" : "No" ;
				// echo 'Check coming from form ' . $this -> quality_agree . '<br />';
 				// Update the team and year codes right away
				// Remember current team, current year
				$this -> team_code = $this -> post_values['team_code'];
				$this -> my_year = $this -> post_values['year_code'];
				$this -> id = $this -> post_values['id'];
	
				$this -> indicator_set[ 'team_code' ] -> value = $this -> team_code;
				$this -> indicator_set[ 'year_code' ] -> set_value( $this -> my_year );


				// Remember old team, year.
				// These are entered into the team form to be carried forward to next refresh
				$this -> team_form -> old_team_code = $this -> team_code;
				$this -> team_form -> old_year_code = $this -> my_year;
				$this -> old_team_code = $this -> post_values['old_team_code'];
				$this -> old_year_code = $this -> post_values['old_year_code'];

				// Check if this is a new team and/or year
				$this -> same_team =  (  $this -> team_code ==  $this -> old_team_code ) ? true : false;
				$this -> same_year =  (  $this -> my_year ==  $this -> old_year_code ) ? true : false;

				if ( isset($this -> post_values['new_record']) ){
					$this -> new_record = true;
					$this -> id = NULL;
					$this -> valid_team = 0;
					$this -> team_code = "";
					$this -> short_form = true;
					$this -> register_flag = false;
					$this -> first_time = true;
				} else {
					$this -> new_record = false;
					$this -> refresh_record();
				}
				//   Do all the processing
		}


		/**
		 * This function is key to directing data into and out of the database.
		 * @return [type] [description]
		 */
		public function refresh_record() {

			// Get results from the database into db results array
			$this -> load_db_results();
			if ( $this -> db_record['save_status'] === 'locked'){
				$this -> msg = 'This data has already been submitted and may not be altered.';
			} elseif ( $this -> db_record['save_status'] === 'blocked'){
				$this -> msg = 'This data may not be altered.';
			}

			// If the page is already displaying a record from the database
			if ( $this -> post_values['id'] !== NULL ){
				if  ( ( $this -> db_record['save_status'] != 'locked') 
					and ( $this -> db_record['save_status'] != 'blocked') ){
					if ( !$this -> toggle_rates_quality){
						$this -> update_form_vars_to_db( $this -> my_year );
					}
					$this -> load_db_results();
				}
				$this -> valid_team = 1 ;
			} else {
				// Otherwise see if the team code is in the database
				// If there is at least one record for the team, it will try to open it
				// If there is no team in the database, this will will allow the team to be added.
				if ( !$this -> register_flag ) {
					$this -> check_valid_team();
				} else {
					$this -> valid_team = 1;
				}
			}
			if ( $this -> valid_team  > 0){
				// Check if the year code is in the database
				// This retrieves the requested period.
				// If it is a new period, it loads blank data
				$this -> check_valid_year();

				$this -> update_indicators_from_db();
				$this -> quality_agree = $this -> db_record['quality_agree'];
				
				$this -> validate_indicators();
			}
		}

		public function load_db_results() {
			// Fils the db_record
			global $wpdb;
			$sql = 'SELECT * FROM indicators WHERE team_code =  "' . $this -> team_code . '"';
			$temp_results = $wpdb -> get_results( $sql, ARRAY_A );
			foreach ( $temp_results as $vars ) {
				$this -> db_results[$vars['year_code']] = $vars;
			}
			// Find the number of occurrences of this team code
			$keys = array_keys( $this -> db_results );
			// print_r($keys);
			// Find the first occurence of a record and extract team code if its found
			$this -> db_team = $this -> db_results[$keys[0]] ;

			 // Search the list of records for an occurence of the selected period
			$found_year = array_search($this -> my_year, $keys );
			if (count($keys) == 0) {
// echo "Team not found at all for " . $this -> my_year . "</br>";
				$this -> db_record = NULL;
				if ($this -> my_year == $this -> latest_year_code ){
					$this -> latest_year_requested = 2;
				} else {
					$this -> latest_year_requested = 1;
				}
			}
			elseif($found_year === false) {
// echo "team found but not as requested  " . $this -> my_year . "</br></br>";
				$this -> db_record['team_code'] = $this -> team_code;
				if ($this -> my_year == $this -> latest_year_code ){
					$this -> latest_year_requested = 2;
				} else {
					$this -> latest_year_requested = 1;
				}
			} else {
// echo "team found as requested - opening the record for " . $this -> my_year . "</br></br>";
				$this -> db_record = $this -> db_results[$this -> my_year] ;
				$this -> latest_year_requested = 0;
			}
			$this -> save_status = $this -> db_record['save_status'];
		}

		// Update the database with the latest post data
		public function update_form_vars_to_db( $period ) {
			// Build the sql query from the indicator set
			$entries = 'UPDATE indicators SET save_date = NOW(), 
			save_status = "' . $this -> save_status .  '", ';
			$comma = '';
			foreach ( $this -> indicator_set as $ind ) {
				if (1) {
					$value = $ind -> save_to_db( $this -> post_values ) ;
					if ($value != NULL){
						$entries .=  $comma . $value;
						$comma = ", ";
					}
				}

			}

			$entries = $entries . '  WHERE id = "' .  $this -> db_record['id'] . '";';
			global $wpdb;
			$wpdb -> query( $entries );
			//echo $entries;
			// echo 'Check going back to db '. $entries . '<br />';
			// echo 'Using ' . $this -> my_year . '</br>';
		}

		// Add a new record to the database, based on form data
		public function insert_form_vars_to_db() {
			$this -> my_year = $this -> latest_year_code;
			$this -> team_code = $this -> post_values['team_code'];
			global $wpdb;
			$wpdb -> insert( 'indicators',
				array(
					'team_code' => $this -> team_code,
					'save_status' => 'saved',
					// 'year_code' => $this -> post_values['year_code']
					'year_code' => $this -> my_year
				) ,
				array(
					'%s',
					'%s'
				)
			);
		}
		
		/**
		 * Look for an existing record in the database for this team_code
		 * If there is one, set the flag for a valid team and store the id.
		 * @return [type] [description]
		 */
		public function check_valid_team() {

// echo "</br>db record</br>";
// print_r($this -> db_record);

			$this -> valid_team = 0;
			if ( ! $this -> first_time ) {
				// validate team code
				$code_from_post = $this ->post_values['team_code'];
				if ( $this -> register_flag ){
					//  This will register the new record for the team code at the selected year
					$code_from_db = $code_from_post;
				} else {
					$code_from_db = $this -> db_team['team_code'];
					//  Check if there is a record for this team at the selected period.
				}
				
				// set the flag if the request is for the current year
				$is_requested_year = ($this -> my_year == $this -> latest_year_code);
				$year_from_db = $this -> db_record['year_code'];
				$year_from_post = $this -> post_values['team_code'];
				$request = $this -> latest_year_requested;
				$valid_result = $this -> indicator_set[ 'team_code' ] -> validate( $code_from_post, $code_from_db, $request );
// echo "</br>db result</br>";
// print_r($valid_result);

				// If this is a valid team for display, $this -> valid_team is set to  1
				// If validation allows a new team record, $this -> valid_team  is set to 2
				// All other results are set to 0
				if ($valid_result['valid'] == 'valid' ) { // This record is in the database
					$this -> valid_team = 1;
					$this -> id = $this -> db_team['id'];
				}
				// 
				elseif (  ( ($valid_result['valid'] == 'valid_new_year' ) // There is an earlier record
							or ($valid_result['valid'] == 'valid_new_code' ) ) // There is no earlier record
							and ($this -> register_flag == false) ){
 					array_push( $this -> error_msg, $valid_result["message"] );
// echo "</br>valid message: " . $errors . "</br>";
// print_r($this -  error_msg);
					$this -> valid_team = 2;
					$this -> id = $this -> db_team['id'];
					$this -> db_record['year_code'] = $this -> my_year;
				} else {
 					array_push( $this -> error_msg, $valid_result["message"] );
					$this -> valid_team = 0;
					$this -> team_code = '';
					$this -> id = NULL;
					$this -> db_record['year_code'] = $this -> my_year;
				}
			}
			return ;
		}


		// Look for an existing record in the database for this team_code
		// If there is none, insert a new record for that year
		public function check_valid_year() {
			if ( ( $this -> db_results['year_code'] == $this -> latest_year_code ) 
				and ( ! $this -> first_time ) 
				and ($this -> valid_team > 0) ) {
				// $this -> insert_form_vars_to_db();
				global $wpdb;
				$rewRecord = $wpdb -> query( 'SELECT * FROM indicators ORDER BY id DESC LIMIT 1');
				$this -> id = $new_record['id'];
			}
			// $this -> id = $this -> db_team['id'];
			$this -> id = $this -> db_record['id'];

		}

		// Move the most recent post data into the indicators
		public function update_indicators_from_form() {
			foreach ( $this -> indicator_set as $ind ) {
				$ind -> retrieve_indicator_from_form( $this -> post_values );
			}
		}

		// Move the most recent database data into the indicators
		public function update_indicators_from_db() {
			foreach ( $this -> indicator_set as $ind ) {
				$ind -> retrieve_from_db ( $this -> db_record );
			}
			$this -> save_status = $this -> db_record['save_status'];
		}

		// Validate all the indicators in the existing posr
		public function validate_indicators( $value='' ) {
			// $this -> error_msg = array();
			$errors = array();
			$exclusions = array(
				"team_code",
				"year_code",
				"setting",
				"tesching",
				"hosp_EMR",
				"quality_agree",
				"X7_day_text"
			);
			foreach ( $this -> indicator_set as $ind ) {
				// echo 'Indicator ' . $ind -> short_label . '<p></p>';
				if (  ( $ind -> indicator_group != 'header' ) 
					and ($ind -> indicator_group != 'profile') 
					and ($ind -> short_label != 'X7_day_text') 
					and ($ind -> short_label != 'quality_agree' ) ) {
						$err =  $ind -> validate();
						if ( $err["valid"] == 'invalid' ) {
							// array_push( $errors, $err["message"] );
							array_push( $this -> error_msg, $err["message"] );
						}
				}
			}

			// array_push($this -> error_msg, $errors);
		}

		public function save_latest_post() {
			// If the year is the same
			// and the team is the same,
			// save the latest form data
			if ( !$this -> short_form ){ //} and $this -> same_team and $this -> same_year ) {
				$this -> update_form_vars_to_db( $this -> my_year );
			}
		}

		public function show_new_team_form() {
			// If this is not the first session
			if ( !( $this -> first_time ) ) {
				$this -> short_form = true;
				$this -> new_team_form -> render_form( $code_from_post );
			}
		}

		///////////////////////////////////////////////
	// RENDER FORMS
		/**
		 * [render_forms description]
		 *
		 * @return [type] [description]
		 */
		public function render_forms() {
		//  If ready to submit
			if ( $this -> send_data_flag){
				$this -> send_data();
			} 

		//  If registering a new team
			if ( $this -> register_flag ) {
				$this -> insert_form_vars_to_db();
				$this -> valid_team = 1;
				$this -> msg = '<em>'  . $this -> team_code . '</em> has been registered as new team code for period ' 	. $this -> my_year;
			}

			$this -> display_message();
			// Show the team form set_value

			if ( $this -> confirm_submit_flag ) {
				$this -> confirm_form -> render_form();
			} else {
				if ( $this -> submit_flag ) {
					if ( ( $this -> save_status != 'locked')
						and ( $this -> db_record['save_status'] !== 'blocked') ){
						$this -> submit_form -> render_form( true );
					} else {
						$this -> msg = 'Team data has already been submitted.';
					}
				}
				$this -> display_errors();
				// If we don't have a valid team and this is not the first of the session
				if ( ($this -> valid_team  < 1 ) 
					and !$this -> first_time  ) {
					$this -> short_form = true;
					if ( $this -> valid_team )
					$this -> team_form -> render_form( true );
				} elseif ( ($this -> valid_team  > 1 ) 
					and !$this -> first_time  ) {
					$this -> new_team_form -> render_form( $code_from_post );
				}
			}

				// If we have a valid record
				// if ( ( $this -> valid_team ) and ( !$this -> short_form ) ) {
			if (  $this -> valid_team  == 1 ) {
				$this -> team_form -> load_button_label = "Save this data";
				$this -> team_form -> render_form( false );
				$this -> profile_form -> render_form();
				// Check if rates or quality
				$this -> q_button_form -> render_form( $this -> rates_quality_flag  );
				// $this -> display_errors();
				if ( $this -> rates_quality_flag == 'quality') {
					$this -> quality_form -> render_form();
				} else {
					$this -> data_form -> render_form();
					$this -> base_form -> render_form();
				}
			} else {
				$this -> team_form -> load_button_label = "Load new data";
				$this -> team_form -> render_form( true );


			}
			echo '</div>';
		}

		public function send_data() {

			$sql = 'UPDATE indicators SET save_status = "locked" ,
			share_agree = "' . $this -> post_values['confirm_review'] . '"
			WHERE 	id = ' . $this -> id . ';';

			global $wpdb;
			$wpdb -> query( $sql );
			// echo $sql;
			echo 'Data locked';



			// Set the flag
			$this -> msg = 'You have successfully submitted your data to the D2D upload platform. 
			The data can no longer be modified but you can come back to the platform at any time 
			to view the data submitted. Watch the AFHTO website for updates. ';
		}



		///////////////////////////////////////////////////
	// UTILITY FUNCTIONS
		/**
		 * Display occasional message
		 *
		 * @return [type] [description]
		 */
		private function display_message( ) {
			if ( $this -> msg ) {
				echo '<div class="d2dcontentHeader"><h4 style="color: red">' . $this -> msg . '</h2></div>';
			}
			return;
		}

		private function display_errors() {
			$keys = array_keys( $this -> error_msg );
			if ( count( $keys > 0 ) ) {
				echo '<div class="d2dcontentHeader"><h5 style="color: #9E0B0F">';
				foreach ( $keys as $k ) {
					echo '<p>' . $this -> error_msg[$k ]. '</p>';
				}
				echo '</h5></div>';
			}
			return;
		}


		// Create the shortcode for this plugin
		public function data_shortcode() {
			ob_start();
			$this -> process_functions();
			return ob_get_clean();
		}

	}
}

if ( class_exists( 'D2D_input' ) ) {
	$d2d_input = new D2D_input();
}