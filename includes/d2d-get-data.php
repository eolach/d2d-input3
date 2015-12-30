<?php
//  This implements the Fetch data class that manages the retrieval of database
//  indicators for display in the review portion of the D2D plugin.
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

if ( !class_exists( 'D2D_fetch_data' ) ) {
	class D2D_fetch_data {

	// variables and lables
		public $team_code;
		public $setting;
		public $teaching;
		public $year_code;
		public $hosp_EMR;
		public $num_pts;
		public $quality_agree;
		public $core_only;

		public $team_results = array();
		public $team_trend_results;
		public $peer_results;
		public $total_results;
		Public $d2d_values;

		public $response = 'Response';
		private	$simple_labels = array('My team', 'Peer average', 'D2D average');
		private	$std_colors = array('#282D42', '#FBB958', '#812620');
		private	$trend_colors = array('#A00961', '#8AA961', '#8AA1B1');


		public $core_d2d_inds = array();

		public $cost_inds = array(
			// 'cost',
			// 'cost_adj',
			// 'cost_prim',
			// 'cost_serv',
			// 'cost_settings',
			// 'cost_inst'
		);

		public $qual_inds = array();

		private $qual_rollup_labels = array(
				'indicator' => "Quality",
				'short_label' => 'overall'
		);

		private $cost_rollup_labels = array(
				'indicator' => "Cost (adjusted)",
				'short_label' => 'cost_adj'
		);

		private $qual_drill_labels = array(
			array(
				'indicator' => "overall",
				'short_label' => 'overall'
				),
			array(
				'indicator' => "Access",
				'short_label' => 'access'
				),
			array(
				'indicator' => "Sensitivity",
				'short_label' => 'sensitivity'
				),
			array(
				'indicator' => "Trust",
				'short_label' => 'trust'
				),
			array(
				'indicator' => "Knowledge",
				'short_label' => 'knowledge'
				),
			array(
				'indicator' => "Commitment",
				'short_label' => 'commitment'
				),
			array(
				'indicator' => "Collaboration",
				'short_label' => 'collaboration'
				)
		);

		private $cost_drill_labels = array(
			array(
				'indicator' => "Total cost",
				'short_label' => 'cost'
				),
			array(
				'indicator' => "Cost (adjusted)",
				'short_label' => 'cost_adj'
				),
			array(
				'indicator' => "Primary care",
				'short_label' => 'cost_prim'
				),
			array(
				'indicator' => "Services",
				'short_label' => 'cost_serv'
				),
			array(
				'indicator' => "Settings",
				'short_label' => 'cost_settings'
				),
			array(
				'indicator' => "Institutions",
				'short_label' => 'cost_inst'
				)
		);

		private $pat_centered_labels = array();

		private $effectiveness_labels = array();

		private $access_labels = array();

		private $integration_labels = array();

		private $data_qual_labels = array(
				'indicator' => "Team quality",
				'short_label' => 'emr_q_cervical',
				'hyperlink' => 'http://www.afhto.ca/members-only/emr-data-quality'
		);
		
		private $sami_labels = array(
				'indicator' => "Team quality",
				'short_label' => 'sami_score'
			);

	// Constructor and utilities
		public function __construct() {

			global $d2d_data_specs;
			$this -> core_d2d_inds = $d2d_data_specs -> make_tab_group("core_d2d_inds");
			$this -> cost_inds = $d2d_data_specs -> make_tab_group("cost_inds");
			$this -> qual_inds = array(
				'overall',
				'access',
				'sensitivity',
				'trust',
				'knowledge',
				'commitment',
				'collaboration'
			);

			$this -> pat_centered_labels = $d2d_data_specs -> make_chart("pat_centered");
			$this -> effectiveness_labels = $d2d_data_specs -> make_chart("effectiveness");
			$this -> access_labels = $d2d_data_specs -> make_chart("access");
			$this -> integration = $d2d_data_specs -> make_chart("integration");

		}

		/**
		 * generate triple of random numbers for test purposes
		 * @return [type] [description]
		 */
		private function make_vars(){
			$temp_vars = array();
			for ( $x = 0; $x < 3; $x++){
				array_push($temp_vars, rand( 50, 80) );
			}
			return $temp_vars;
		}

		// Test function outputs test material to web page
		/**
		 * This displays the raw output of the fetch operation
		 * It needs helper echoes turned on
		 * @return [type] [description]
		 */
		public function test_get_data(){
			echo 'Validated php <br>';
			
			$this -> read_post( $this -> test_vars );

			$this -> retrieve_data_sets( 'external' );

			$this -> d2d_values = $this -> build_d2d_ind_values();

			$this -> save_quality_indicators();

			$response = $this -> build_charts();
			echo '<br><br>Response:<br>';
			print_r( $response );

			die(0);
		} 

		/**
		 * A test function that by-passes the full web page and displays the JSON insteaf
		 * This function is called by the alternative shortcode
		 * @return [type] [description]
		 */
		public function test_wp_get_data(){
			echo 'Confirmed valid php <br>';
			$this -> read_post( $this -> test_vars );

			$this -> retrieve_data_sets( 'wp' );

			$this -> d2d_values = $this -> build_d2d_ind_values();

			$response = $this -> build_charts();

			print_r($response);

			return $response;
		}

		// Live function - send data to the charts
		/**
		 * This is the permanaent process that initiates the changes 
		 * to the review page on the website
		 * @param  [type] $post_data [description]
		 * @return [type]            [description]
		 */
		public function process_data( $post_data ){

			global $D2D_review;

			$this -> read_post( $post_data );

			$this -> retrieve_data_sets( 'wp' );

			if ($this -> validate_team_code()){

				$this -> d2d_values = $this -> build_d2d_ind_values();

				$response = json_encode($this -> build_charts());
			} else {
				$fail_response = array(
					'fail_code' => 'not_found',
					'team_code' => $this->team_code //$post_data['team_code']
					);
				$response = json_encode($fail_response);
			}

			return $response;
		}

		public $test_vars = array(
				'team_code' => '#RWER07',
				'setting' => 'Urban',
				'teaching' => 'Non-teaching',
				'year_code' => 'D2D 2.0',
				'hosp_emr' => 'Yes',
				'num_pts' => '10k_30k',
				'rost_all' => 'total',
				'core_only' => 'd2d_only'
		);


		// *
		//  * The original function called by the jQuery.
		//  * No longer used
		//  * @return [type] [description]
		 
		// public function respond(){
		// }


	// Manage date retrieval
	

		/**
		 * Brings in the $POST variables
		 * @param  array  $peer_vars [description]
		 * @return [type]            [description]
		 */
		public function read_post( array $peer_vars ){
			// $this -> team_code = $this -> test_vars['team_code'];
			$this -> team_code = $peer_vars['team_code'];

			// $this -> setting   = $this -> test_vars['setting'];
			$this -> setting   = $peer_vars['setting'];

			// $this -> teaching  = $this -> test_vars['teaching'];
			$this -> teaching  = $peer_vars['teaching'];

			// $this -> year_code = $this -> test_vars['year_code'];
			$this -> year_code = $peer_vars['year_code'];

			// $this -> hosp_emr  = $this -> test_vars['hosp_emr'];
			$this -> hosp_emr  = $peer_vars['hosp_emr'];

			// $this -> num_pts   = $this -> test_vars['num_pts'];
			$this -> num_pts   = $peer_vars['num_pts'];

			$this -> rost_all  = $this -> test_vars['rost_all'];
			// $this -> rost_all  = $peer_vars['rost_all'];
			
			// 
			// $this -> core_only = $this -> test_vars['core_only'];
			$this -> core_only = $peer_vars['core_only'];

		}

		/**
		 * Retrieves teh data from the database, either through new connection
		 * or through $wpdb.
		 * @param  string $source [description]
		 * @return [type]         [description]
		 */
		public function retrieve_data_sets( $source = 'wp' ){


			// Team query for current iteration
			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked" AND team_code =  "' . $this -> team_code . '"
			AND year_code =  "' . $this -> year_code . '"';

			// Team query for trends
			$sql_team_trend = 'SELECT * FROM indicators WHERE save_status = "locked" AND team_code =  "' . $this -> team_code . '"';
			
			// // Peer query
			$sql_peer = 'SELECT * FROM indicators WHERE save_status = "locked" ';
			
			
			if ( $this ->  year_code  != 'none' ){
				$sql_peer .= ' AND year_code =  "' . ($this -> year_code)  . '"';
			} 
			if ( $this ->  teaching != 'none' ){
				$sql_peer .= ' AND teaching =  "' . ($this -> teaching) . '"';
			} 
			if ( $this ->  setting != 'none' ){
				$sql_peer .= ' AND setting =  "' . ($this -> setting) . '"';
			} 
			if ( $this ->  hosp_emr != 'none'  ){
				if( $this -> hosp_emr == 'No'){
					$sql_peer .= ' AND  hosp_emr = "N_A" OR hosp_emr IS NULL';
				} elseif( $this -> hosp_emr == 'Yes'){
					$sql_peer .= ' AND  hosp_emr IS NOT NULL AND hosp_emr != "N_A"';
				}
			} 
			if ( $this ->  num_pts != 'none'   ){
				if( $this -> num_pts == 'lt_10k'){
					$sql_peer .= ' AND  pts_rostered <= 10000 AND  pts_rostered IS NOT NULL ';
				} elseif( $this -> num_pts == '10k_30k'){
					$sql_peer .= ' AND  pts_rostered >= 10000 AND  pts_rostered <= 30000 ';
				} elseif ( $this -> num_pts == 'gt_30k' ){
					$sql_peer .= ' AND  pts_rostered >= 30000 ';
				}
			} 

			// // Total query
			$sql_total = 'SELECT * FROM indicators WHERE save_status = "locked" AND 
				year_code =  "' . $this -> year_code  . '"';

			$sql_weights = 'SELECT * FROM d2d_weights_levels';

			// Choose db connection context
			// 
			if ($source != 'wp'){
				$servername = 'wplocal';
				$username = 'root';
				$password ='jeQiRPIT';
				$dbname = 'wordpress1';
				$conn = new mysqli( $servername, $username, $password, $dbname );

				if ($conn -> connect_error){
					die("Connection failed: " . $conn -> connect_error);
				}
				
				$results = $conn -> query( $sql_team );
				// echo 'Team rows ' . $results -> num_rows . '<br>';
				$this -> team_results = array();
				while ( $row = $results -> fetch_assoc( )){
					$this -> team_results[] = $row;
				}

				$results = $conn -> query( $sql_team_trend);

				// echo 'Team trend rows ' . $results -> num_rows;
				$this -> team_trend_results = array();
				while ( $row = $results -> fetch_assoc( )){
					$this -> team_trend_results[] = $row;
				}

				$results = $conn -> query( $sql_peer );
				// echo 'Peer rows ' . $results -> num_rows;
				$this -> peer_results = array();
				while ( $row = $results -> fetch_assoc( )){
					$this -> peer_results[] = $row;
				}
				
				$results = $conn -> query( $sql_total);
				// echo 'Totsl rows ' . $results -> num_rows;
				$this -> total_results = array();
				while ( $row = $results -> fetch_assoc( )){
					$this -> total_results[] = $row;
				}

				$results = $conn -> query( $sql_weights);
				$this -> weights_levels = array();
				while ( $row = $results -> fetch_assoc( )){
					$this -> weights_levels[] = $row;
				}
				mysqli_close( $conn );
			} else {
				global $wpdb;
				$this -> team_results       = $wpdb -> get_results( $sql_team, ARRAY_A );
				$this -> team_trend_results = $wpdb -> get_results( $sql_team_trend, ARRAY_A );
				$this -> peer_results       = $wpdb -> get_results( $sql_peer, ARRAY_A );

				$this -> total_results      = $wpdb -> get_results( $sql_total, ARRAY_A );
				$this -> weights_levels     = $wpdb -> get_results( $sql_weights, ARRAY_A );
			}
			// echo 'team sql ' . $sql_team . '<br>';
			// echo 'Peer sql ' . $sql_peer . '<br>';


			// echo  'Team results: <br>';
			// print_r($this -> team_trend_results );
			// echo  '<br><br>' . $this -> teaching . ' <br>';
			// echo  '<br><br>' . $this -> setting . ' <br>';
			// echo  '<br><br>' . $this -> hosp_emr . ' <br>';
			// echo  '<br><br>' . $this -> num_pts . ' <br>';
			// echo  '<br><br>Peer results: <br>';
			// print_r($this -> peer_results );
		}

		
		private function validate_team_code( ){ 
			if ( ( $this -> team_code == NULL) ||( $this -> team_code == "") || ( count( $this -> team_results ) > 0 )  ){
				$this -> quality_agree = $this -> team_results[0]['quality_agree'];
				// $this -> setting = $this -> team_results[0]['setting'];
				// $this -> teaching = $this -> team_results['teaching'];
				// $this -> hosp_emr = $this -> team_results['hosp_emr'];
				// $this -> num_pts = $this -> team_results['num_pts'];
				return true;
			} else {
				return false;
			}
		}



// Prepare the individual stats
	
		/**
		 * [builds the arrays of variables that will be used in the display
		 * of the core D2D indicators]
		 * @return [none] [This set of values is available globally
		 * as $this -> d2d_values]
		 */
		private function build_d2d_ind_values(){
			$values_array = array();
			
			// Compute core D2D
			foreach( $this -> core_d2d_inds as $ind){
				$values_array[ $ind ] = $this -> build_stats( $ind, 'hex' );
			}
			// print_r($values_array);

			// Compute Cost
			foreach( $this -> cost_inds as $ind){
				$values_array[ $ind ] = $this -> build_stats( $ind, 'cost_drill' );
			}
			
			// For computational efficiency, the quality composite indicators are
			// calculated as a set for each record and managed separately for adding
			// to the main d2d_in_values collection .
			// Note that this fucntion definition 
			$this -> build_qual_stats( $values_array, $this -> qual_inds);

			return $values_array;
		}


				/**
		 * Calculate_starfield is a 
		 * For computational efficiency the set of composite indicators is calculated
		 * at the same time for a given record.
		 * @return [array] array of triplets for each of the quality composite indicators
		 * in 'qual_drill'
		 */


		private function build_simple_stats( $indicator_labels ){
			$vals = $this -> extract_indicator( $indicator_labels['short_label'], 'cost_qual', NULL);
			
			$indicator = array(
				'team'  => number_format( $vals[0], 2),
				'peers' => number_format( $vals[1], 2),
				'total' => number_format( $vals[2], 2)
				);

			return $indicator;
		}


		/**
		 * A special function that replaces the build_stats 
		 * function called on all the other indicators to add to the main collection
		 * of indicator values, d2d_ind_values.
		 * The triplets are implemented by using a special case in extract_indicator,
		 * where the sets of individual composites are passed in instead of the raw data
		 * that otherwise would come directly from the database.
		 * @param  [reference to array] &$val_array [the main collection of d2d-indicator values]
		 * @param  [array] $indicator_lables [list of the indicators in this group]
		 * @return [nothing]  The funcion modifies the collection array by reference
		 */
		public function build_qual_stats( &$val_array, $indicator_labels ){
			// This first pass retrieves a set of triplets for each of the team, peer and
			// total indicators
			$temp_ind_set = array(
				'team'  => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> team_results ),
				'peers' => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> peer_results ),
				'total' => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> total_results )
				);
			// This secord pass distributes the tripets to each of the 
			// respective composite indicators
			foreach( $indicator_labels as $ind){
				$indicator_set = array(
					'team'  => $temp_ind_set['team'][ $ind ],
					'peers' => $temp_ind_set['peers'][ $ind ],
					'total' => $temp_ind_set['total'][ $ind ]
					);
				$val_array[ $ind ] = $indicator_set;
			}
			// print_r($val_array);

		// 	return;
		}

		/**
		 * General function to build the statistics for a given indicator
		 * Extracts the rate for rostered and all patients
		 * and calculates the peer average and total average for the 
		 * indicator.
		 * NOTE: Quality indicators are prepared and added to the main collection separately.
		 * @param  [string] $ind_name [short label for an indicator]
		 * @param  [string] $ind_type [simple for single value, hex for 
		 * @param  [string] $ind_type [simple for single value, hex for 
		 * rostered and all, special for immunization which needs to report 
		 * both at the same time]
		 * @return [array]           [three vales for team, peers and total]
		 */
		public function build_stats( $ind_name, $ind_type ){
			$indicator_set = array(
				'team'  => $this -> extract_indicator( $ind_name, $ind_type, $this -> team_results ),
				'peers' => $this -> extract_indicator( $ind_name, $ind_type, $this -> peer_results ),
				'total' => $this -> extract_indicator( $ind_name, $ind_type, $this -> total_results )
				);
			
			$trends = $this -> team_trend_results;
			$keys = array_keys( $trends);
			foreach($keys as $k){
				$iteration_label = $trends[$k]['year_code'];
				$indicator_set[ $iteration_label ] = 
						$this -> extract_indicator( $ind_name, 'trend', $trends[$k] );
			}
			return $indicator_set;
		} 

		public function check_extended_inds( $this_row ){
			
			$found_extended = false;
			$return_flag = true;
			$ref_list_inds = $this -> weights_levels;
			foreach( $ref_list_inds as $ref_ind){
				if( !(in_array( $ref_ind['short_label'], $this -> core_d2d_inds))){
					if ( ($this_row[ $ref_ind['short_label'] ] != NULL) && ($this_row[ $ref_ind['short_label']  ] != '_____') ){
						$found_extended = true;
						break;
					}
				}
			}
			if( $this -> core_only == 'd2d_only' ){
				if ($found_extended){
					$return_flag = false;
				}
			} else {
				if (! ($found_extended) ){
					$return_flag = false;
				}
			}
			// }
			return $return_flag;
		}

		/**
		 * Takes a single indicator and a set of results
		 * and returns the average for the rostered and total versions of that indicator.
		 * If the indicator is a statistic for a collection of teams, e.g., peers or total,
		 * the function detects that by the size of the array passed in.
		 * If its a simple indicator, it returns just one triplet for team, peer and total.
		 * If it's a hex indicator, it returns a pair of triplets, one each for all patients and
		 * rostered patients.
		 * @param  [string] $ind_name  [name of the target indicator]
		 * @param  [type] $ind_type  [type - either single or hex]
		 * @param  [type] $ind_array [the set of results to be processes - 
		 * individual team (a one-row array) or peer or total results]
		 * @return [array]            ['rostered' or 'total']
		 */
		public function extract_indicator( $ind_name, $ind_type, $ind_array ){

			$indicator = NULL;

			switch ($ind_type){
				
				case 'cost_drill':

					$temp_array = array();
					if ( count ( $ind_array) > 0 ){
						foreach ($ind_array as $row ){
							$temp_entry = $row[ $ind_name ] ;
							array_push( $temp_array, $temp_entry);
						}
					} else {
							array_push( $temp_array, NULL);
					}

					$running_total = 0;
					$running_count = 0;
					foreach( $temp_array as $row){
						if ( $row  != 0) {
							$running_total +=  $row;
							$running_count ++;
						}
					}
					$temp_total1 = $running_total/( $running_count ? $running_count : 1) ;
					$temp_total = number_format((float)$temp_total1, 2, '.', '');

					$indicator = $temp_total;
					// echo '<br>' . $ind_name . '<br>';
					// print_r($indicator);
					break;

				case 'qual_drill':

					$temp_return = array();
					// this will extract all the indicators in the array passed in as $ind_name
					// for the array of database results for passed in as $ind_array
					$temp_array = array();
					if ( count ( $ind_array) > 0 ){ // there is at least one record
						foreach ($ind_array as $row ){
							// this is where the composite indicators are prepared
							// from the combination of indicators in the record
							// 
							// Test if this row is to be included
							// i.e., does it contain extended data,
							// and is the extended data flag set?
							if ( $this -> check_extended_inds( $row )){
								$temp_entry =  $this -> calculate_starfield( $ind_name, $row ) ;
								array_push( $temp_array, $temp_entry);
							}
						}
					} else {
							array_push( $temp_array, NULL);
					}

					// For each of the composite indicators
					foreach( $ind_name as $ind ){
						$running_total = 0;
						$running_count = 0;
						foreach( $temp_array as $temp_row){
							if ( $temp_row[ $ind ]['total'] > 0) {
								$running_total +=  $temp_row[ $ind ]['total'];
								$running_count++;
							}
						}
						$temp_total1 = number_format($running_total/( $running_count ? $running_count : 1), 2) ;
						$temp_total = number_format((float)$temp_total1, 2, '.', '');

						$temp_return[ $ind ] = $temp_total;
					}

					$indicator = $temp_return;

			
					break;

				case 'cost_qual':

					$ind_out_array = array();
					// Get the values from the result arrays
					// Team
					if ( $this -> team_results){
							$temp_team = $this -> team_results[0][$ind_name];
							// $temp_team = $this -> team_results[$ind_name];
					} else {
						$temp_team = NULL;
					}
					array_push( $ind_out_array, $temp_team );
					
					// Peers
					$running_total = 0;
					$running_count = 0;
					foreach( $this -> peer_results as $row){
						if ( $row[$ind_name] != 0) {
							$running_total +=  $row[$ind_name];
							$running_count++;
						}
					}
					$temp_peers1 = $running_total/( $running_count ? $running_count : 1);
					$temp_peers = number_format((float)$temp_peers1, 2, '.', '');
					array_push( $ind_out_array, $temp_peers );

					// Total
					$running_total = 0;
					$running_count = 0;
					foreach( $this -> total_results as $row){
						if ( $row[$ind_name] != 0) {
							$running_total +=  $row[$ind_name];
							$running_count ++;
						}
					}
					$temp_total1 = $running_total/( $running_count ? $running_count : 1);
					$temp_total = number_format((float)$temp_total1, 2, '.', '');
					array_push( $ind_out_array, $temp_total);

					$indicator = $ind_out_array;

					break;

				case 'hex':

					// Flag to catch special case of chaild immunization where
					// rostered numbers are to be captured even though they are ignored elsewhere
					$imm_flag = false;
					if($ind_name == 'child_imm_rost'){
						$ind_name = 'child_imm';
						$imm_flag = true;

						// echo 'Processing imm_rost' . '<br>';					
					}

					// Routine
					// Decode each of the entries for this indicator
					$temp_array = array();
					if ( count ( $ind_array) > 0 ){
						foreach ($ind_array as $row ){
							$temp_entry = $this -> d2d_decode( $row[ $ind_name ] ) ;
							array_push( $temp_array, $temp_entry);
						}
					} else {
							$temp_entry = $this -> d2d_decode( '-----' ) ;
							array_push( $temp_array, $temp_entry);
					}


					$averages_array = array(
						'rostered' => 0,
						'rost_count' => 0,
						'total' => 0,
						'total_count' => 0
						);
					foreach( $temp_array as $row){
						if ( $row['rostered'] != 0) {
							$averages_array['rostered'] =  $averages_array['rostered'] + $row['rostered'];
							$averages_array['rost_count'] = $averages_array['rost_count'] + 1;
						}
						if ( $row['total'] != 0) {
							$averages_array['total'] =  $averages_array['total'] + $row['total'];
							$averages_array['total_count'] = $averages_array['total_count'] + 1;
						}
					}
					// Prepare the array of rostered and total averages

					$ind_out_array = array();
					$ind_out_array['name'] = $ind_name;

					$ind_out_array['total'] = $averages_array['total' ] /
									( $averages_array['total_count'] ? $averages_array['total_count'] : 1);
					$ind_out_array['rostered'] = $averages_array['rostered' ] /
									( $averages_array['rost_count'] ? $averages_array['rost_count'] : 1);
					if ($imm_flag){
						$ind_out_array['total'] = $averages_array['rostered' ] /
										( $averages_array['rost_count'] ? $averages_array['rost_count'] : 1);
						$ind_out_array['name'] = 'child_imm_rost';
						// echo 'Naming imm_rost' . $ind_out_array['name'] . '<br>';					
					}
					

					$indicator = $ind_out_array;
					break;

				case 'trend':
					
					// The array passed in $ind_array in this case is only a single row,
					// and only one indicator is identified in the call
					$temp_array = array();
					$temp_entry = $this -> d2d_decode( $ind_array[ $ind_name ] ) ;

					$ind_out_array = array();
					$ind_out_array['name'] = $ind_name;

					$ind_out_array['total'] = $temp_entry['total' ];
					$ind_out_array['rostered']= $temp_entry['rostered' ];

					// print_r($ind_out_array);
					$indicator = $ind_out_array;
	

					break;

			}
			return $indicator;
		}


		/**
		 * Decodes the packed hex data in the database
		 * @param  string $hex_code [six number separated by underscore]
		 * @return array  of values for rostered and total patients
		 * Note that the order of values in the pack is total first
		 * and then rostered.
		 * */
		private function d2d_decode( $hex_code ){
			 $stats_set = explode('_', $hex_code);
			 $stats = array(
			 	'rostered' => $stats_set[5],
			 	'total'    => $stats_set[2]
			 	);
			 return $stats;
		}


		/**
		 * Special function, called only by the offline test page,
		 * that generates the quality indicators for each team 
		 * for the D2D 2.0 iteration, and saves those values
		 * to the database as a new table.
		 * @return [type] [description]
		 */
		public function save_quality_indicators(){
			// The results for every team is already available
			// in $this -> total_results
			echo '<table>';
			echo '<th> Team </th><th>Overall</th><th>Access</th><th>Sensitity</th><th>Trust</th>';
			echo '<th>Knowledg</th><th>Commitment</th><th>Collaboration</th>';
			$qual_inds_array = array();


				$servername = 'wplocal';
				$username = 'root';
				$password ='jeQiRPIT';
				$dbname = 'wordpress1';
				$conn = new mysqli( $servername, $username, $password, $dbname );


			foreach( $this -> total_results as $team){
				// echo $team['team_code'] . '<br>';
				$temp_array = array();
				$temp_inds = $this -> calculate_starfield( $this -> qual_inds, $team);
				$keys = array_keys($temp_inds);
				foreach($keys as $k){
					$temp_array[$k] = $temp_inds[$k]['total'];
				}
				// array_push($qual_inds_array, $temp_array);
				// var_dump($temp_array);
				echo '<tr><td>'. $team["team_code"] . '</td><td>' . $temp_array["overall"] . '</td><td>' . $temp_array["access"] . '</td><td>' . $temp_array["sensitivity"] . '</td>';
				echo '<td>' . $temp_array["trust"] . '</td><td>' . $temp_array["knowledge"] . '</td><td>' . $temp_array["commitment"] . '</td><td>' .$temp_array["collaboration"] . '</td></tr>';
			// Write to the database
				$temp_code = $team["team_code"];

				$sql = 'INSERT INTO d2d_qual_results (team_code, overall, access, sensitivity, trust, knowledge,
					commitment, collaboration) VALUES ("'. $temp_code . '", ' .
					$temp_array["overall"] . ', ' .
					$temp_array["access"] . ', ' .
					$temp_array["sensitivity"] . ', ' .
					$temp_array["trust"] . ',  ' .
					$temp_array["knowledge"] . ', ' .
					$temp_array["commitment"] . ', ' .
					$temp_array["collaboration"] . 
					')';


					if ($conn->query($sql) === TRUE) {
					    echo "New record created successfully";
					} else {
					    echo "Error: " . $sql . "<br>" . $conn->error;
					}
			}
			// 
		}

		public function use_extended_indicator( $this_indicator ){
			$return_flag = true;
			
			if( $this -> core_only == 'd2d_only' ){
				if( !(in_array( $ref_ind['short_label'], $this -> core_d2d_inds))){
					$return_flag = false;
				}
			}
			return $return_flag;
		}


		/**
		 * [calculate_starfield does the heavy lifting in analysing a single record
		 * for the quality composities. Note that it returns the results as an array of 
		 * values, one each for the composite indicators, The extract function handles this
		 * distinction.]
		 * @param  [array] $indicator_labels [list of the indicators to be returned]
		 * @param  [array] $row [single record from the database]
		 * @return [array]                   [Array of values for the set of indicators]
		 */
		public function calculate_starfield( $indicator_labels, $result_row){
			$weights = $this -> weights_levels; // Directly from the databse
			
			$temp_array = array();
			$accum_total = array();
			$accum_weight = array();
			foreach($weights as $weights_row){
				// Iterate over the  compositie indicators
				// Note that the overall must be computed first
				// since it is needed in subsequent iterations

				// If this is an extended indicator, and we're looking at
				// extended, then use it.
				// 
				// if (use_extended_indicator){

				foreach( $indicator_labels as $composite){
					$active_inds = 0;
					$mounting_total  = 0;
					$mounting_weight = 0;

					$temp_ind_name  = $weights_row['short_label'];
					$temp_weight   = $weights_row[ $composite ];
					$temp_both_vals = $this -> d2d_decode( $result_row[ $temp_ind_name ] );
					$temp_value0 = $temp_both_vals['total'];

					$temp_value = $this -> d2d_apply_threshold( $temp_value0, $weights_row['min'], $weights_row['max']);

					if( $temp_value > 0 ){
						$active_inds++;
						$mounting_total  += ($temp_weight * $temp_value);
						$mounting_weight += $temp_weight;
					}
					$accum_total[ $composite ] += $mounting_total;
					$accum_weight[ $composite ] += $mounting_weight;
				}
				// }
			}

			// Find the weighted averages
			foreach( $indicator_labels as $comp){
				// $v_accum_weight = $accum_weight[ $comp] ? $accum_weight[$comp] : 1;
				$v_accum_weight = $accum_weight[ 'overall'] ? $accum_weight['overall'] : 1;
				$comp_ind = $accum_total[ $comp]/$v_accum_weight;
				$temp_array[$comp] = $comp_ind;
			}

			// Assemble return array
			$return_array = array();
			foreach( $indicator_labels as $ind){
				// echo 'Finished with ' . $ind . ' row: ' . $temp_array[$ind] . '<br><br>';
				$return_array[ $ind ] = array(
					'total' => $temp_array[$ind],
					'rostered' => $temp_array[$ind]
					);
				// );
			}


			
			return $return_array;
		}

		public function d2d_apply_threshold( $val, $low, $high){
			if($val < $low){
				$new_val = 0;
			}elseif($val > $high){
				$new_val = 100;
			} else {
				$new_val = 100 * ((($high - $low) > 0) ? ($val - $low)/($high - $low) : 1);
			}

			return $new_val;
		}



 //  Build the charts
		/**
		 * Final assemply of the JSON code to be sent back to jQuery
		 * @return [type] [description]
		 */
		public function build_charts(){
			$all_charts = array();
			array_push($all_charts, $this -> build_quality_chart( $this -> qual_rollup_labels) );
			array_push($all_charts, $this -> build_group_chart( $this -> qual_drill_labels, 'simple') );
			array_push($all_charts, $this -> build_simple_chart( $this -> cost_rollup_labels) );
			array_push($all_charts, $this -> build_group_chart( $this -> cost_drill_labels, 'simple') );
			array_push($all_charts, $this -> build_group_chart( $this -> pat_centered_labels, 'hex' ) );
			array_push($all_charts, $this -> build_group_chart( $this -> effectiveness_labels, 'hex' ) );
			array_push($all_charts, $this -> build_group_chart( $this -> access_labels, 'hex' ) );
			array_push($all_charts, $this -> build_group_chart( $this -> integration_labels, 'hex' ) );
			array_push($all_charts, $this -> build_trend_chart( $this -> pat_centered_labels, 'hex' ) );
			array_push($all_charts, $this -> build_trend_chart( $this -> effectiveness_labels, 'hex' ) );
			array_push($all_charts, $this -> build_trend_chart( $this -> access_labels, 'hex' ) );
			array_push($all_charts, $this -> build_trend_chart( $this -> integration_labels, 'hex' ) );
			array_push($all_charts, $this -> build_simple_stats( $this -> data_qual_labels) );
			array_push($all_charts, $this -> build_simple_stats( $this -> sami_labels) );
			array_push($all_charts, $this -> build_peer_inds() );
			array_push($all_charts, $this -> build_table() );

			
			return $all_charts;
		}

		private function build_simple_chart( $indicator_labels ){
			$inds = $this -> simple_labels;
			$cols = $this -> std_colors;
			$vals = $this -> extract_indicator( $indicator_labels['short_label'], 'cost_qual', NULL);
			$keys = array_keys($inds);
			
			$chart_array = array();
			for ($k = 0; $k < count($inds); $k++){
				$temp_array = array(
					'Indicator' => $inds[$k],
					'color' => $cols[$k],
					'Score' => $vals[$k]
					);
				array_push($chart_array,  $temp_array);
			}
			return $chart_array;
		}

		private function build_quality_chart( $indicator_labels ){
			$inds = $this -> simple_labels;
			$cols = $this -> std_colors;
			// $keys = array_keys($inds);
			$vals = $this -> d2d_values[$indicator_labels['short_label']];
			$keys = array_keys($vals);
			$chart_array = array();
			if ( (array_values($indicator_labels) == array_values($this -> qual_rollup_labels) )
				&& ( $this -> quality_agree != 'Yes'  ) ) {
				$vals[$keys[0]] = 0;
			}

			for ($k = 0; $k < count($inds); $k++){
				$temp_array = array(
					'Indicator' => $inds[$k],
					'color' => $cols[$k],
					'Score' => $vals[$keys[$k]]
					);
				array_push($chart_array,  $temp_array);
			}

			return $chart_array;
		}


		/**
		 * Build the chartdata for a chart that has data grouped for each indicator
		 * The grous are always three values for my_team, peer_avg, total_avg
		 * @param  [string] $ind_array associative array of indicator short codes
		 * @param  [num] $val_array [description]
		 * @return [type]            [description]
		 */
		private function build_group_chart( array $indicator_labels, $type){
			$inds = array();
			$links = array();
			$vals = array();
			foreach ( $indicator_labels as $ind){
				array_push($inds, $ind['indicator']);
				array_push($links, $ind['hyperlink']);
				$val_array = array();
				// Distinguihing between hex and simple indicators
				if ( $type == 'hex' ){
					$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['team'][$this -> rost_all], 1)  ;
					$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['peers'][$this -> rost_all], 1 ) ;
					$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['total'][$this -> rost_all], 1 ) ;
				} else {
					$val_array[] = $this -> d2d_values[$ind ['short_label']]['team']  ;
					$val_array[] = $this -> d2d_values[$ind ['short_label']]['peers'] ;
					$val_array[] = $this -> d2d_values[$ind ['short_label']]['total'] ;
				}
				array_push( $vals, $val_array ) ;
				// echo '<br>Local val_array: <br>';				
				// print_r( $val_array );
				// echo '<br>Full vals array: <br>';				
				// print_r( $vals );
				// echo '<br>End of array <br>';
			}
			
			$chart_array = array();
			

			for( $k = 0; $k < count( $inds ); $k++){
				if ( (array_values($indicator_labels) == array_values($this -> qual_drill_labels) )
					&& ( $this -> quality_agree != 'Yes'  ) ) {
					$vals[$k][0] = 0;
				}
				$temp_array = array(
					'indicator' => $inds[$k],
					'this_team' => $vals[$k][0],
					'peer_average' => $vals[$k][1],
					'total_average' => $vals[$k][2],
					'url' => $links[$k]
					);
				array_push($chart_array,  $temp_array);
			}
			// echo 'Chart array: ' . '<br>';;				
			// print_r( $chart_array );
			// echo '<br>End of chart array: ' . '<br><br>';;				
			return $chart_array;
		}

		/** Build the chartdata for a chart that has data trende for each indicator
		 * The grous are always four values for d2d_1, d2d_2, d2d_3, d2d_4
		 * @param  [string] $ind_array associative array of indicator short codes
		 * @param  [num] $val_array [description]
		 * @return [type]            [description]
		 */
		private function build_trend_chart( array $indicator_labels){

			$inds = array();
			$vals = array();

			foreach ( $indicator_labels as $ind){
				array_push($inds, $ind['indicator']);
			//echo ' building trend for ' . $ind['indicator'] . '<br>';

				// Scan the d2d_values for iterations
				$keys = array_keys($this -> d2d_values[$ind ['short_label']]);
				$val_array = array();
				foreach($keys as $k){
					if( substr($k, 0, 3) == 'D2D'){
						$val_array[ $k ] = $this -> d2d_values[$ind ['short_label']][$k]['total'];
					}
				}
				array_push( $vals, $val_array );
			}
			
			$chart_array = array();
			for( $k = 0; $k < count($inds); $k++){
				$temp_array = array(
					'indicator' => $inds[$k],
					'd2d_1' => $vals[$k]['D2D 1.0'] *100,
					'd2d_2' => $vals[$k]['D2D 2.0'],
					'd2d_3' => NULL,
					'd2d_4' => NULL,
					);
				array_push($chart_array,  $temp_array);
			}
			// print_r( $chart_array );


			return $chart_array;
		}


		
		public function build_peer_inds(){
			$peer_inds = array(
				'setting' => $this -> setting,
				'teaching' => $this -> teaching,
				'hosp_emr' => $this -> hosp_emr,
				'num_pts' => $this ->  num_pts
				);
			return $peer_inds;
		}

		/**
		 * Build the table using the values calculated in the d2d_fetch_data object in d2d-get-data.php
		 * @return array(array) Array of indicator values for each of the fixed indicators in the table.
		 */
		private function build_table(){
			// $values = $D2D_fetch_data -> d2d_values;
			$t_view = array();
			$t_row = array(
				'PCPMF'   => 'Effectiveness',
				'CoreD2D' => 'Cervical Ca screening',
				'Team'    => 70,
				'Peer'    => 75,
				'Peer_N'  => NULL,
				'Peer_SAMI' => NULL,
				'D2D'     => 80,
				'D2D_N'   => NULL,
				'D2D_SAMI'    => NULL,
				'D2D_range'    => 75
				);
			array_push($t_view, $t_row);
			return $t_view;
		}





	}
}


