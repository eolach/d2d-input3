<?php
//  This implements the Fetch data class that manages the retrieval of database
//  indicators for display in the review portion of the D2D plugin.
/**
 *
 *
 * @author Neil
 * @version 0.5
 * @created 03-June-2015 10:06 AM
 * @updated 02-Jan-2016
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
		public $peer_N = 123;
		public $D2D_N = 456;
		public $D2D_range = '123-126';
		public $iteration = 3;
		public $d2d_values;

		public $response = 'Response';
		private $simple_labels = array( 'My team', 'Peer average', 'D2D average' );
		private $std_colors = array( '#282D42', '#FBB958', '#812620' );
		private $trend_colors = array( '#A00961', '#8AA961', '#8AA1B1' );


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
			'short_label' => 'overall',
			'hyperlink' =>'http://www.afhto.ca/members-only/quality-roll-up-indicator-measuring-the-quality-of-comprehensive-primary-care/'
		);

		private $cost_rollup_labels = array(
			'indicator' => "Cost (adjusted)",
			'short_label' => 'cost_adj',
			'hyperlink' => 'http://www.afhto.ca/members-only/cost/'
		);

		private $qual_drill_labels = array(
			array(
				'indicator' => "overall",
				'short_label' => 'overall',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Access",
				'short_label' => 'access',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Sensitivity",
				'short_label' => 'sensitivity',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Trust",
				'short_label' => 'trust',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Knowledge",
				'short_label' => 'knowledge',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Commitment",
				'short_label' => 'commitment',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			),
			array(
				'indicator' => "Collaboration",
				'short_label' => 'collaboration',
				'hyperlink' => 'http://www.afhto.ca/wp-content/uploads/Quality-roll-up-Indicator-FAQ-and-6-domains.pdf'
			)
		);

		private $cost_drill_labels = array(
			array(
				'indicator' => "Total cost",
				'short_label' => 'cost',
				'hyperlink' => 'http://www.afhto.ca/members-only/cost/'
			),
			array(
				'indicator' => "Cost (adjusted)",
				'short_label' => 'cost_adj',
				'hyperlink' => ''
			),
			array(
				'indicator' => "Primary care",
				'short_label' => 'cost_prim',
				'hyperlink' => ''
			),
			array(
				'indicator' => "Services",
				'short_label' => 'cost_serv',
				'hyperlink' => ''
			),
			array(
				'indicator' => "Settings",
				'short_label' => 'cost_settings',
				'hyperlink' => ''
			),
			array(
				'indicator' => "Institutions",
				'short_label' => 'cost_inst',
				'hyperlink' => ''
			)
		);

		private $pat_centered_labels = array();

		private $effectiveness_labels = array();

		private $access_labels = array();

		private $integration_labels = array();

		private $data_qual_labels = array(
			'cervical' => array(
				'indicator' => "Team quality",
				'short_label' => 'emr_q_cervical'),
			'colorectal' => array(
				'indicator' => "Team quality",
				'short_label' => 'emr_q_colorectal'),
			'smoking' => array(
				'indicator' => "Team quality",
				'short_label' => 'emr_q_smoking')
		);
		

		private $sami_labels = array(
			'indicator' => "Team quality",
			'short_label' => 'sami_score',
			'hyperlink' => 'http://www.afhto.ca/members-only/sami-score/'
		);

		// Constructor and utilities
		public function __construct() {

			$this -> qual_inds = array(
				'overall',
				'access',
				'sensitivity',
				'trust',
				'knowledge',
				'commitment',
				'collaboration'
			);

			// $this -> make_all_labels();

			// $iteration = 2;
			// $this -> pat_centered_labels = $d2d_data_specs -> make_chart("pat_centered", $iteration);
			// $this -> effectiveness_labels = $d2d_data_specs -> make_chart("effectiveness", $iteration);
			// $this -> access_labels = $d2d_data_specs -> make_chart("access", $iteration);
			// $this -> integration_labels = $d2d_data_specs -> make_chart("integration", $iteration);

			// $this -> table_labels = $this -> make_table_labels();

		}

		private function make_all_labels( $iteration ) {
			global $d2d_data_specs;
			$this -> core_d2d_inds = $d2d_data_specs -> make_tab_group( "core_d2d_inds" );
			$this -> cost_inds = $d2d_data_specs -> make_tab_group( "cost_inds" );

			$this -> pat_centered_labels = $d2d_data_specs -> make_chart( "pat_centered", $iteration );
			$this -> effectiveness_labels = $d2d_data_specs -> make_chart( "effectiveness", $iteration );
			$this -> access_labels = $d2d_data_specs -> make_chart( "access", $iteration );
			$this -> integration_labels = $d2d_data_specs -> make_chart( "integration", $iteration );

			$this -> table_labels = $this -> make_table_labels();

			$this -> data_qual_labels = $d2d_data_specs -> make_chart( "emr_data_quality", $iteration );
		}

		private function make_table_labels() {

			$temp_labels = array();
			array_push( $temp_labels, $this -> effectiveness_labels );
			array_push( $temp_labels, $this -> pat_centered_labels );
			array_push( $temp_labels, $this -> access_labels );
			array_push( $temp_labels, $this -> integration_labels );
			return $temp_labels;
		}

		/**
		 * generate triple of random numbers for test purposes
		 *
		 * @return [type] [description]
		 */
		private function make_vars() {
			$temp_vars = array();
			for ( $x = 0; $x < 3; $x++ ) {
				array_push( $temp_vars, rand( 50, 80 ) );
			}
			return $temp_vars;
		}

		// Test function outputs test material to web page
		/**
		 * This displays the raw output of the fetch operation
		 * It needs helper echoes turned on
		 *
		 * @return [type] [description]
		 */
		public function test_get_data() {
			echo 'Testing data response <br>';

			$this -> read_post( $this -> test_vars );

			$this -> retrieve_data_sets( 'external' );

			$this -> d2d_values = $this -> build_d2d_ind_values();

			$this -> save_quality_indicators();

			$response = $this -> build_charts();
			echo '<br><br>Response:<br>';
			print_r( $response );

			die( 0 );
		}

		/**
		 * A test function that by-passes the full web page and displays the JSON insteaf
		 * This function is called by the alternative shortcode
		 *
		 * @return [type] [description]
		 */
		public function test_wp_get_data() {

			$this -> read_post( $this -> test_vars );

			$this -> retrieve_data_sets( 'wp' );

			$this -> make_all_labels( $this -> iteration );

			$this -> table_labels = $this -> make_table_labels();


			$this -> d2d_values = $this -> build_d2d_ind_values();

			// // $this -> exportQualityToCSV();

			$response = $this -> build_charts();


			return $response;
		}

		// Live function - send data to the charts
		/**
		 * This is the permanaent process that initiates the changes
		 * to the review page on the website
		 *
		 * @param [type]  $post_data [description]
		 * @return [type]            [description]
		 */
		public function process_data( $post_data ) {

			global $D2D_review;

			$this -> read_post( $post_data );

			$this -> retrieve_data_sets( 'wp' );

			if ( $this -> validate_team_code() ) {

				$this -> make_all_labels( $this -> iteration );

				$this -> d2d_values = $this -> build_d2d_ind_values();


				$response = json_encode( $this -> build_charts() );
			} else {
				$fail_response = array(
					'fail_code' => 'not_found',
					'team_code' => $this->team_code //$post_data['team_code']
				);
				$response = json_encode( $fail_response );
			}

			return $response;
		}

		public $test_vars = array(
			'team_code' => 'gregtest2',
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
		 *
		 * @param array   $peer_vars [description]
		 * @return [type]            [description]
		 */
		public function read_post( array $peer_vars ) {
			// $this -> team_code = $this -> test_vars['team_code'];
			$this -> team_code = $peer_vars['team_code'];

			// $this -> setting   = $this -> test_vars['setting'];
			$this -> setting   = $peer_vars['setting'];

			// $this -> teaching  = $this -> test_vars['teaching'];
			$this -> teaching  = $peer_vars['teaching'];

			// $this -> year_code = $this -> test_vars['year_code'];
			$this -> year_code = $peer_vars['year_code'];

			// Convert year_code to number
			$this -> iteration =  $this -> convert_year_code_to_iteration( $this -> year_code );

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
		 * Converts the string to the number inside
		 *
		 * @param [string] $str [year-code from post]
		 * @return [number]      [number for use in specifying indicators for the selected iteration]
		 */
		private function convert_year_code_to_iteration( $str ) {
			// preg_match_all('/\d+/', $str, $matches);
			// return (int)$matches[1];
			switch ( $str ) {
			case "D2D 1.0":
				return 1;
				break;

			case "D2D 2.0":
				return 2;
				break;

			case "D2D 3.0":
				return 3;
				break;

			case "D2D 4.0":
				return 4;
				break;



			}
		}

		/**
		 * Retrieves teh data from the database, either through new connection
		 * or through $wpdb.
		 *
		 * @param string  $source [description]
		 * @return [type]         [description]
		 */
		public function retrieve_data_sets( $source = 'wp' ) {


			// Team query for current iteration
			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked" AND team_code =  "' . $this -> team_code . '"
			AND year_code =  "' . $this -> year_code . '"';

			// Team query for trends
			$sql_team_trend = 'SELECT * FROM indicators WHERE save_status = "locked" AND team_code =  "' . $this -> team_code . '"';

			// // Peer query
			$sql_peer = 'SELECT * FROM indicators WHERE save_status = "locked" ';


			if ( $this ->  year_code  != 'none' ) {
				$sql_peer .= ' AND year_code =  "' . ( $this -> year_code )  . '"';
			}
			if ( $this ->  teaching != 'none' ) {
				$sql_peer .= ' AND teaching =  "' . ( $this -> teaching ) . '"';
			}
			if ( $this ->  setting != 'none' ) {
				$sql_peer .= ' AND setting =  "' . ( $this -> setting ) . '"';
			}
			if ( $this ->  hosp_emr != 'none'  ) {
				if ( $this -> hosp_emr == 'No' ) {
					$sql_peer .= ' AND  hosp_emr = "N_A" OR hosp_emr IS NULL';
				} elseif ( $this -> hosp_emr == 'Yes' ) {
					$sql_peer .= ' AND  hosp_emr IS NOT NULL AND hosp_emr != "N_A"';
				}
			}
			if ( $this ->  num_pts != 'none'   ) {
				if ( $this -> num_pts == 'lt_10k' ) {
					$sql_peer .= ' AND  pts_rostered <= 10000 AND  pts_rostered IS NOT NULL ';
				} elseif ( $this -> num_pts == '10k_30k' ) {
					$sql_peer .= ' AND  pts_rostered >= 10000 AND  pts_rostered <= 30000 ';
				} elseif ( $this -> num_pts == 'gt_30k' ) {
					$sql_peer .= ' AND  pts_rostered >= 30000 ';
				}
			}

			// // Total query
			$sql_total = 'SELECT * FROM indicators WHERE save_status = "locked" AND
				year_code =  "' . $this -> year_code  . '"';

			// $sql_weights = 'SELECT * FROM d2d_weights_levels';
			$sql_weights = 'SELECT * FROM d2d_weights3_0';

			// Choose db connection context
			//
			if ( $source != 'wp' ) {
				$servername = 'wplocal';
				$username = 'root';
				$password ='jeQiRPIT';
				$dbname = 'wordpress1';
				$conn = new mysqli( $servername, $username, $password, $dbname );

				if ( $conn -> connect_error ) {
					die( "Connection failed: " . $conn -> connect_error );
				}

				$results = $conn -> query( $sql_team );
				// echo 'Team rows ' . $results -> num_rows . '<br>';
				$this -> team_results = array();
				while ( $row = $results -> fetch_assoc( ) ) {
					$this -> team_results[] = $row;
				}

				$results = $conn -> query( $sql_team_trend );

				// echo 'Team trend rows ' . $results -> num_rows;
				$this -> team_trend_results = array();
				while ( $row = $results -> fetch_assoc( ) ) {
					$this -> team_trend_results[] = $row;
				}

				$results = $conn -> query( $sql_peer );
				// echo 'Peer rows ' . $results -> num_rows;
				$this -> peer_results = array();
				while ( $row = $results -> fetch_assoc( ) ) {
					$this -> peer_results[] = $row;
				}

				$results = $conn -> query( $sql_total );
				// echo 'Totsl rows ' . $results -> num_rows;
				$this -> total_results = array();
				while ( $row = $results -> fetch_assoc( ) ) {
					$this -> total_results[] = $row;
				}

				$results = $conn -> query( $sql_weights );
				// Weights_levels are read from a static table named "d2d_weights_levels" in the wp database
				// containing the weights to be assigned in calculating the quality indicators.
				// It also includes as levels the upper and lower boundaries for each indicator.
				$this -> weights_levels = array();
				while ( $row = $results -> fetch_assoc( ) ) {
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
			// echo '</br>' . $sql_team . '</br>';
			// print_r($this -> team_results);
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
			// print_r($this -> weights_levels );
		}


		private function validate_team_code( ) {
			// if (  $this -> team_code == "saveQuality" ){
			// 	$this -> exportQualityToCSV();
			// }
			if ( ( $this -> team_code == NULL ) 
				||( $this -> team_code == "" )
				|| ( count( $this -> team_results ) > 0 )  ) {
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
		 *
		 * @return [none] [This set of values is available globally
		 * as $this -> d2d_values]
		 */
		private function build_d2d_ind_values() {
			$values_array = array();

			// Compute core D2D
			foreach ( $this -> core_d2d_inds as $ind ) {
				$values_array[ $ind ] = $this -> build_stats( $ind, 'hex' );
			}

			// Compute Cost
			foreach ( $this -> cost_inds as $ind ) {
				$values_array[ $ind ] = $this -> build_stats( $ind, 'cost_drill' );
			}

			// For computational efficiency, the quality composite indicators are
			// calculated as a set for each record and managed separately for adding
			// to the main d2d_in_values collection .
			// Note that this fucntion definition
			$this -> build_qual_stats( $values_array, $this -> qual_inds );

			return $values_array;
						print_r($values_array);

		}


		private function build_emrqual_stats( $indicator_labels ) {

			$count_val = count( $indicator_labels);
			$top_vals = array( 
				'team' => 0 , 
				'peers' => 0, 
				'total' => 0
				);
			$low_vals = array();

			foreach ($indicator_labels as $ind){
				$ind_k = $ind['short_label'];
				$low_val = $this -> extract_indicator( $ind_k, 'cost_qual', NULL ); 
				$low_vals[] = array(
					'team'  => $low_val[0],
					'peers' => $low_val[1],
					'total' => $low_val[2]
					);
			}

			$keys = array_keys($top_vals);

			foreach ($keys as $k) {
				for($i = 0; $i < 3; $i++){
			// 	// foreach ($inds_k as $ik){
					$top_vals[$k] = $top_vals[$k] + $low_vals[$i][$k];
				}
				$top_vals[$k] = number_format( $top_vals[$k]/$count_val, 2 );
			

			}
			return $top_vals;
		}


		private function build_simple_stats( $indicator_labels ) {
			$vals = $this -> extract_indicator( $indicator_labels['short_label'], 'cost_qual', NULL );

			$top_vals = array(
				'team'  => number_format( $vals[0], 2 ),
				'peers' => number_format( $vals[1], 2 ),
				'total' => number_format( $vals[2], 2 )
			);
			return $top_vals;
		}


		/**
		 * A special function that replaces the build_stats
		 * function called on all the other indicators to add to the main collection
		 * of indicator values, d2d_ind_values.
		 * The triplets are implemented by using a special case in extract_indicator,
		 * where the sets of individual composites are passed in instead of the raw data
		 * that otherwise would come directly from the database.
		 *
		 * @param [reference to array] &$val_array [the main collection of d2d-indicator values]
		 * @param [array] $indicator_lables [list of the indicators in this group called "qual_inds"]
		 * @return [nothing]  The funcion modifies the collection array by reference
		 */
		public function build_qual_stats( &$val_array, $indicator_labels ) {
			// echo "Building quality " . count($indicator_labels) . "</br>";
			// This first pass retrieves a set of triplets for each of the team, peer and
			// total indicators
			$temp_ind_set = array(
				'team'  => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> team_results ),
				'peers' => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> peer_results ),
				'total' => $this -> extract_indicator( $indicator_labels, 'qual_drill', $this -> total_results )
			);
			// This secord pass distributes the tripets to each of the
			// respective composite indicators
			foreach ( $indicator_labels as $ind ) {
				// echo "Processing " . $ind . "</br>";
				$indicator_set = array(
					'team'  => $temp_ind_set['team'][ $ind ],
					'peers' => $temp_ind_set['peers'][ $ind ],
					'total' => $temp_ind_set['total'][ $ind ]
				);
				$val_array[ $ind ] = $indicator_set;
			}
			// echo "</br>val_array </br>";
			// print_r($val_array);

			 return;
		}

		/**
		 * General function to build the statistics for a given indicator
		 * Extracts the rate for rostered and all patients
		 * and calculates the peer average and total average for the
		 * indicator.
		 * NOTE: Quality indicators are prepared and added to the main collection separately.
		 *
		 * @param [string] $ind_name [short label for an indicator]
		 * @param [string] $ind_type [simple for single value, hex for
		 * @param [string] $ind_type [simple for single value, hex for
		 * rostered and all, special for immunization which needs to report
		 * both at the same time]
		 * @return [array]           [three vales for team, peers and total]
		 */
		public function build_stats( $ind_name, $ind_type ) {


			$indicator_set = array(
				'team'  => $this -> extract_indicator( $ind_name, $ind_type, $this -> team_results ),
				'peers' => $this -> extract_indicator( $ind_name, $ind_type, $this -> peer_results ),
				'total' => $this -> extract_indicator( $ind_name, $ind_type, $this -> total_results ),
			);

			$trends = $this -> team_trend_results;
			$keys = array_keys( $trends );
			foreach ( $keys as $k ) {
				$iteration_label = $trends[$k]['year_code'];
				$indicator_set[ $iteration_label ] =
					$this -> extract_indicator( $ind_name, 'trend', $trends[$k] );
			}
			$indicator_set['peer_N'] = count( $this -> peer_results );
			$indicator_set['D2D_N'] = count( $this -> total_results );
			$indicator_set['D2D_range'] = $this -> find_range( $this -> total_results, $ind_name );
			return $indicator_set;
		}

		/**
		 * Find the range of an array of values
		 *
		 * @param [array] $results [array of values]
		 * @return [string]          [Min to max values]
		 */
		public function find_range( $results, $ind ) {


			$temp_min = 10000000;
			$temp_max = 0;
			foreach ( $results as $row ) {
				if ($ind == 'diabetes_core'){
					$temp_value =  $row[ $ind ] * 100;
				}else {
					$temp_values =  $this -> d2d_decode( $row[ $ind ] ) ;
					$temp_value = $temp_values['total'];
				}
				// echo 'Values' . $temp_values['total'];
				// print_r($temp_values);
				// echo '</br>';
				if ( ( !is_null( $temp_value ) ) and ( is_numeric( $temp_value ) ) ) {
					if ( $temp_value < $temp_min ) $temp_min = $temp_value;
					if ( $temp_value > $temp_max ) $temp_max = $temp_value;
				}
			}
			// echo 'Results';
			// print_r($this -> $results[0][$ind]);
			if ($temp_min == 10000000 ){
				return ("---");
			} else {
				return $temp_min . ' - ' . $temp_max;
			}
		}

		public function check_extended_inds( $this_row ) {

			$found_extended = false;
			$return_flag = true;
			$ref_list_inds = $this -> weights_levels;
			foreach ( $ref_list_inds as $ref_ind ) {
				if ( !( in_array( $ref_ind['short_label'], $this -> core_d2d_inds ) ) ) {
					if ( ( $this_row[ $ref_ind['short_label'] ] != NULL ) && ( $this_row[ $ref_ind['short_label']  ] != '_____' ) ) {
						$found_extended = true;
						break;
					}
				}
			}
			if ( $this -> core_only == 'd2d_only' ) {
				if ( $found_extended ) {
					$return_flag = false;
				}
			} else {
				if ( ! ( $found_extended ) ) {
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
		 *
		 * @param [string] $ind_name  [name of the target indicator]
		 * @param [type]  $ind_type  [type - either single or hex]
		 * @param [type]  $ind_array [the set of results to be processes -
		 * individual team (a one-row array) or peer or total results]
		 * @return [array]            ['rostered' or 'total']
		 */
		public function extract_indicator( $ind_name, $ind_type, $ind_array ) {

			$indicator = NULL;

			if ($ind_name == "diabetes_care"){
				$ind_type = 'hex';
			}

			switch ( $ind_type ) {

			case 'cost_drill':

				$temp_array = array();
				if ( count( $ind_array ) > 0 ) {
					foreach ( $ind_array as $row ) {
						$temp_entry = $row[ $ind_name ] ;
						array_push( $temp_array, $temp_entry );
					}
				} else {
					array_push( $temp_array, NULL );
				}

				$running_total = 0;
				$running_count = 0;
				foreach ( $temp_array as $row ) {
					if ( $row  != 0 ) {
						$running_total +=  $row;
						$running_count ++;
					}
				}
				$temp_total1 = $running_total/( $running_count ? $running_count : 1 ) ;
				$temp_total = number_format( (float)$temp_total1, 2, '.', '' );

				$indicator = $temp_total;
				// echo '' . $ind_name . '<br>';
				// print_r($indicator);
				break;
				
			case 'qual_drill':
				// echo "qual_drill array : </br>";
				// print_r($ind_array);
				
				global $D2D_manage_data;
				$D2D_manage_data -> build_tables();


				$temp_return = array();
				// this will extract all the indicators in the array passed in as $ind_name
				// for the array of database results for passed in as $ind_array
				$temp_array = array();
				if ( count( $ind_array ) > 0 ) { // there is at least one record
					foreach ( $ind_array as $row ) {
						// this is where the composite indicators are prepared
						// from the combination of indicators in the record
						//
						// Test if this row is to be included
						// i.e., does it contain extended data,
						// and is the extended data flag set?
				// echo 'row ' . $row['team_code'] . '';
				// 
				// Next condition removed 2016-01-11 to include all records in calculation
						// if ( $this -> check_extended_inds( $row ) ) {
							// $temp_entry =  $this -> calculate_starfield( $ind_name, $row ) ;
							$temp_entry =  $D2D_manage_data -> get_quality_indicator($ind_name, 
								$row['team_code'], 
								$this -> year_code ) ;
							array_push( $temp_array, $temp_entry );
						// }
					}


				} else {
					array_push( $temp_array, NULL );
				}

				// For each of the composite indicators
				foreach ( $ind_name as $ind ) {
					// echo 'ind ' . $ind . '</br>';
					$running_total = 0;
					$running_count = 0;
					foreach ( $temp_array as $temp_row ) {
						if ( $temp_row[ $ind ]['total'] == 0 ) {
							$temp_row[ $ind ]['total'] = $this -> impute_missing_data( $ind_name, $temp_array );

						}
						$running_total +=  $temp_row[ $ind ]['total'];
						$running_count++;
					}
					$temp_total1 = number_format( $running_total/( $running_count ? $running_count : 1 ), 2 ) ;
					$temp_total = number_format( (float)$temp_total1, 2, '.', '' );

					$temp_return[ $ind ] = $temp_total;
				}

				$indicator = $temp_return;
				// print_r($ind_array);
				// echo "End </br>";


				break;

			case 'cost_qual':

				$ind_out_array = array();
				// Get the values from the result arrays
				// Team
				if ( $this -> team_results ) {
					$temp_team = $this -> team_results[0][$ind_name];
					// $temp_team = $this -> team_results[$ind_name];
				} else {
					$temp_team = NULL;
				}
				array_push( $ind_out_array, $temp_team );

				// Peers
				$running_total = 0;
				$running_count = 0;
				foreach ( $this -> peer_results as $row ) {
					if ( ( $row[$ind_name] != 0 ) and
					( !is_null($row[$ind_name]) ) ){
						$running_total +=  $row[$ind_name];
						$running_count++;
					}
				}
				$temp_peers1 = $running_total/( $running_count ? $running_count : 1 );
				$temp_peers = number_format( (float)$temp_peers1, 2, '.', '' );
				array_push( $ind_out_array, $temp_peers );

				// Total
				$running_total = 0;
				$running_count = 0;
				foreach ( $this -> total_results as $row ) {
					if ( ( $row[$ind_name] != 0 ) and
					( !is_null($row[$ind_name]) ) ){
						$running_total +=  $row[$ind_name];
						$running_count++;
					}
				}
				$temp_total1 = $running_total/( $running_count ? $running_count : 1 );
				$temp_total = number_format( (float)$temp_total1, 2, '.', '' );
				array_push( $ind_out_array, $temp_total );

				$indicator = $ind_out_array;

				break;

			case 'hex':


				// Flag to catch special case of child immunization where
				// rostered numbers are to be captured even though they are ignored elsewhere
				$imm_flag = false;
				if ( $ind_name == 'child_imm_rost' ) {
					$ind_name = 'child_imm';
					$imm_flag = true;

					// echo 'Processing imm_rost' . '<br>';
				}

				// Routine
				// Decode each of the entries for this indicator
				$temp_array = array();
				if ( count( $ind_array ) > 0 ) {
					foreach ( $ind_array as $row ) {

						if ($ind_name == "diabetes_core"){
							$temp_entry = array(
								'total' => $row[ $ind_name ] *100,
								'rostered' => $row[ $ind_name ] *100
							);
						} else {
							$temp_entry = $this -> d2d_decode( $row[ $ind_name ] ) ;
						}
						array_push( $temp_array, $temp_entry );
					}
				} else {
					$temp_entry = $this -> d2d_decode( '-----' ) ;
					array_push( $temp_array, $temp_entry );
				}


				$averages_array = array(
					'rostered' => 0,
					'rost_count' => 0,
					'total' => 0,
					'total_count' => 0,
				);
				foreach ( $temp_array as $row ) {
					if ( $row['rostered'] != 0 ) {
						$averages_array['rostered'] =  $averages_array['rostered'] + $row['rostered'];
						$averages_array['rost_count'] = $averages_array['rost_count'] + 1;
					}
					if ( $row['total'] != 0 ) {
						$averages_array['total'] =  $averages_array['total'] + $row['total'];
						$averages_array['total_count'] = $averages_array['total_count'] + 1;
					}
				}
				// Prepare the array of rostered and total averages

				$ind_out_array = array();
				$ind_out_array['name'] = $ind_name;

				$ind_out_array['total'] = $averages_array['total' ] /
					( $averages_array['total_count'] ? $averages_array['total_count'] : 1 );
				$ind_out_array['rostered'] = $averages_array['rostered' ] /
					( $averages_array['rost_count'] ? $averages_array['rost_count'] : 1 );
				if ( $imm_flag ) {
					$ind_out_array['total'] = $averages_array['rostered' ] /
						( $averages_array['rost_count'] ? $averages_array['rost_count'] : 1 );
					$ind_out_array['name'] = 'child_imm_rost';
					// echo 'Naming imm_rost' . $ind_out_array['name'] . '<br>';
				}

				$ind_out_array['range'] = $averages_array['rost_min'] . ' - ' . $averages_array['rost_min'];


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
		 * Where a datapoint is missing for a given indicator,
		 * choose a random datapoint from the remaining valid values in the dataset
		 *
		 * @param [string] $ind     [name of the indicator.]
		 * @param [array] $dataset [array of values for the dataset.]
		 * @return [number]          [a value for the datapoing]
		 */
		private function impute_missing_data( $ind, $dataset ) {
			// $valid_set = array();
			// foreach ( $dataset as $ds ) {
			// 	if ( $ds[$ind] != 0 ) {
			// 		array_push( $valid_set, $ds[$ind] );
			// 	}
			// }
			// if ( count( $valid_list ) > 0 ) {
			// 	return array_rand( $valid_set, 1 );
			// } else {
				return 0;
			// }

		}


		/**
		 * Decodes the packed hex data in the database
		 *
		 * @param string  $hex_code [six number separated by underscore]
		 * @return array  of values for rostered and total patients
		 * Note that the order of values in the pack is total first
		 * and then rostered.
		 * */
		private function d2d_decode( $hex_code ) {
			$stats_set = explode( '_', $hex_code );
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
		 *
		 * @return [type] [description]
		 */
		public function save_quality_indicators() {
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
			foreach ( $this -> total_results as $team ) {
				// echo $team['team_code'] . '<br>';
				$temp_array = array();
				$temp_inds = $this -> calculate_starfield( $this -> qual_inds, $team );
				$keys = array_keys( $temp_inds );
				foreach ( $keys as $k ) {
					$temp_array[$k] = $temp_inds[$k]['total'];
				}
				// array_push($qual_inds_array, $temp_array);
				// var_dump($temp_array);
				echo '<tr><td>'. $team["team_code"] . '</td><td>' . $temp_array["overall"] . '</td><td>' . $temp_array["access"] . '</td><td>' . $temp_array["sensitivity"] . '</td>';
				echo '<td>' . $temp_array["trust"] . '</td><td>' . $temp_array["knowledge"] . '</td><td>' . $temp_array["commitment"] . '</td><td>' .$temp_array["collaboration"] . '</td></tr>';
				// Write to the database
				$temp_code = $team["team_code"];

				$sql = 'INSERT INTO d2d_qual_results (team_code, iteration, overall, access, sensitivity, trust, knowledge,
					commitment, collaboration) VALUES ("'. $temp_code . '", "'. $temp_code . '" , "' .
					$temp_array["overall"] . ', ' .
					$temp_array["access"] . ', ' .
					$temp_array["sensitivity"] . ', ' .
					$temp_array["trust"] . ',  ' .
					$temp_array["knowledge"] . ', ' .
					$temp_array["commitment"] . ', ' .
					$temp_array["collaboration"] .
					')';


				if ( $conn->query( $sql ) === TRUE ) {
					echo "New record created successfully";
				} else {
					echo "Error: " .  "<br>" . $conn -> error;
				}
			}
			//
		}

		public function use_extended_indicator( $this_indicator ) {
			$return_flag = true;

			if ( $this -> core_only == 'd2d_only' ) {
				if ( !( in_array( $ref_ind['short_label'], $this -> core_d2d_inds ) ) ) {
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
		 *
		 * @param [array] $indicator_labels [list of the indicators to be returned]
		 * @param [array] $row              [single record from the database]
		 * @return [array]                   [Array of values for the set of indicators]
		 */
		public function calculate_starfield( $indicator_labels, $result_row ) {
			$weights = $this -> weights_levels; // Directly from the databse
			// echo "Weights " . count($weights);
			$temp_array = array();
			$accum_total = array();
			$accum_weight = array();
			foreach ( $weights as $weights_row ) {
				// Iterate over the  composite indicators
				// Note that the overall must be computed first
				// since it is needed in subsequent iterations

				// If this is an extended indicator, and we're looking at
				// extended, then use it.
				//
				// if (use_extended_indicator){

				foreach ( $indicator_labels as $composite ) { //Steps through all the composite indicators
					$active_inds = 0;
					$mounting_total  = 0;
					$mounting_weight = 0;

					$temp_ind_name  = $weights_row['short_label'];
					$temp_weight   = $weights_row[ $composite ];
					$temp_both_vals = $this -> d2d_decode( $result_row[ $temp_ind_name ] );
					$temp_value0 = $temp_both_vals['total'];

					$temp_value = $this -> d2d_apply_threshold( $temp_value0, $weights_row['lower'], $weights_row['upper'] );

					if ( $temp_value > 0 ) {
						$active_inds++;
						$mounting_total  += ( $temp_weight * $temp_value );
						$mounting_weight += $temp_weight;
					}
					$accum_total[ $composite ] += $mounting_total;
					$accum_weight[ $composite ] += $mounting_weight;
				}
				// }
			}

			// Find the weighted averages
			foreach ( $indicator_labels as $comp ) {
				// $v_accum_weight = $accum_weight[ $comp] ? $accum_weight[$comp] : 1;
				$v_accum_weight = $accum_weight[ 'overall'] ? $accum_weight['overall'] : 1;
				$comp_ind = $accum_total[ $comp]/$v_accum_weight;
				$temp_array[$comp] = $comp_ind;
			}

			// Assemble return array
			$return_array = array();
			foreach ( $indicator_labels as $ind ) {
				// echo 'Finished with ' . $ind . ' row: ' . $temp_array[$ind] . '<br><br>';
				$return_array[ $ind ] = array(
					'total' => $temp_array[$ind],
					'rostered' => $temp_array[$ind]
				);
				// );
			}

			return $return_array;
		}

		public function d2d_apply_threshold( $val, $low, $high ) {
			if ( $val < $low ) {
				$new_val = 0;
			}elseif ( $val > $high ) {
				$new_val = 100;
			} else {
				$new_val = 100 * ( ( ( $high - $low ) > 0 ) ? ( $val - $low )/( $high - $low ) : 1 );
			}

			return $new_val;
		}



		//  Build the charts
		/**
		 * Final assemply of the JSON code to be sent back to jQuery
		 *
		 * @return [type] [description]
		 */
		public function build_charts() {
			$all_charts = array();
			array_push( $all_charts, $this -> build_quality_chart( $this -> qual_rollup_labels ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> qual_drill_labels, 'simple' ) );
			array_push( $all_charts, $this -> build_simple_chart( $this -> cost_rollup_labels ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> cost_drill_labels, 'simple' ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> pat_centered_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> effectiveness_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> access_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_group_chart( $this -> integration_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_trend_chart( $this -> pat_centered_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_trend_chart( $this -> effectiveness_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_trend_chart( $this -> access_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_trend_chart( $this -> integration_labels, 'hex' ) );
			array_push( $all_charts, $this -> build_simple_stats( $this -> sami_labels ) );
			array_push( $all_charts, $this -> build_emrqual_stats( $this -> data_qual_labels ) );
			array_push( $all_charts, $this -> build_peer_inds() );
			array_push( $all_charts, $this -> build_table( $this -> table_labels ) );
			array_push( $all_charts, '<center><b>Core D2D ' .$this -> iteration . '.0 indicators</b></center>' );

			return $all_charts;
		}

		private function build_simple_chart( $indicator_labels ) {
			$inds = $this -> simple_labels;
			$cols = $this -> std_colors;
			$vals = $this -> extract_indicator( $indicator_labels['short_label'], 'cost_qual', NULL );
			$keys = array_keys( $inds );

			$chart_array = array();
			// for ( $k = 0; $k < count( $inds ); $k++ ) {
			foreach($keys as $k) {
				$temp_array = array(
					'Indicator' => $inds[$k],
					'color' => $cols[$k],
					'Score' => $vals[$k]
				);
				array_push( $chart_array,  $temp_array );
			}
			return $chart_array;
		}

		private function build_quality_chart( $indicator_labels ) {
			$inds = $this -> simple_labels;
			$cols = $this -> std_colors;
			// $keys = array_keys($inds);
			$vals = $this -> d2d_values[$indicator_labels['short_label']];
			$keys = array_keys( $vals );
			$chart_array = array();
			if ( ( array_values( $indicator_labels ) == array_values( $this -> qual_rollup_labels ) )
				&& ( $this -> quality_agree != 'Yes'  ) ) {
				$vals[$keys[0]] = 0;
			}

			for ( $k = 0; $k < count( $inds ); $k++ ) {
				$temp_array = array(
					'Indicator' => $inds[$k],
					'color' => $cols[$k],
					'Score' => $vals[$keys[$k]]
				);
				array_push( $chart_array,  $temp_array );
			}

			return $chart_array;
		}


		/**
		 * Build the chartdata for a chart that has data grouped for each indicator
		 * The grous are always three values for my_team, peer_avg, total_avg
		 *
		 * @param [string] $ind_array associative array of indicator short codes
		 * @param [num]   $val_array [description]
		 * @return [type]            [description]
		 */
		private function build_group_chart( array $indicator_labels, $type ) {
			$inds = array();
			$links = array();
			$vals = array();
			foreach ( $indicator_labels as $ind ) {
				array_push( $inds, $ind['indicator'] );
				array_push( $links, $ind['hyperlink'] );
				$val_array = array();
				// Distinguihing between hex and simple indicators
				if ( $type == 'hex' ) {
					$val_array[] = number_format( $this -> d2d_values[$ind ['short_label']]['team'][$this -> rost_all], 1 )  ;
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


			for ( $k = 0; $k < count( $inds ); $k++ ) {
				if ( ( array_values( $indicator_labels ) == array_values( $this -> qual_drill_labels ) )
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
				array_push( $chart_array,  $temp_array );
			}
			// echo 'Chart array: ' . '<br>';;
			// print_r( $chart_array );
			// echo '<br>End of chart array: ' . '<br><br>';;
			return $chart_array;
		}

		 /** Build the chartdata for a chart that has data trende for each indicator
		 * The grous are always four values for d2d_1, d2d_2, d2d_3, d2d_4
		 *
		 * @param [string] $ind_array associative array of indicator short codes
		 * @param [num]   $val_array [description]
		 * @return [type]            [description]
		 */
		private function build_trend_chart( array $indicator_labels ) {

			//    echo "Iteration " . $iteration . '</br>';


			// print_r($indicator_labels);

			$inds = array();
			$vals = array();

			foreach ( $indicator_labels as $ind ) {



				array_push( $inds, $ind['indicator'] );
				//echo ' building trend for ' . $ind['indicator'] . '<br>';

				// Scan the d2d_values for iterations
				$keys = array_keys( $this -> d2d_values[$ind ['short_label']] );
				$val_array = array();
				foreach ( $keys as $k ) {
					if ( substr( $k, 0, 4 ) == 'D2D ' ) {
						$val_array[ $k ] = $this -> d2d_values[$ind ['short_label']][$k]['total'];
					}
				}
				array_push( $vals, $val_array );
			}

			$chart_array = array();
			for ( $k = 0; $k < count( $inds ); $k++ ) {
				$temp_array = array(
					'indicator' => $inds[$k],
					'd2d_1' => $vals[$k]['D2D 1.0'] *100,
					'd2d_2' => $vals[$k]['D2D 2.0'],
					'd2d_3' => $vals[$k]['D2D 3.0'],
					'd2d_4' => $vals[$k]['D2D 4.0']
				);
				array_push( $chart_array,  $temp_array );
			}
			// print_r( $chart_array );


			return $chart_array;
		}



		public function build_peer_inds() {
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
		 *
		 * @return array(array) Array of indicator values for each of the fixed indicators in the table.
		 */
		private function build_table( array $indicator_labels ) {
			// $values = $D2D_fetch_data -> d2d_values;
			$t_view = array();
			$pcpmf = array( "Effectiveness", "Patient experience", "Access", "Integration" );
			$index = 0;
			foreach ( $indicator_labels as $group ) {
				foreach ( $group as $ind ) {
					$t_row = array(
						'PCPMF'   => $pcpmf[$index],
						'CoreD2D' => '<a href= "' . $ind["hyperlink"] . '">' . $ind ['indicator'] ,
						'Team'    => number_format( $this -> d2d_values[$ind ['short_label']]['team'][$this -> rost_all], 1 ),
						'Peer'    => number_format( $this -> d2d_values[$ind ['short_label']]['peers'][$this -> rost_all], 1 ),
						'Peer_N'  => $this -> d2d_values[$ind ['short_label']]['peer_N'],
						'Peer_SAMI' => NULL,
						'D2D'     => number_format( $this -> d2d_values[$ind ['short_label']]['total'][$this -> rost_all], 1 ),
						'D2D_N'   => $this -> d2d_values[$ind ['short_label']]['D2D_N'],
						'D2D_SAMI'  => NULL,
						'D2D_range'  => $this -> d2d_values[$ind ['short_label']]['D2D_range']
					);
					array_push( $t_view, $t_row );
				}
				$index++;
			}
			return $t_view;
		}

		// private function exportQualityToCSV(){
		// 	// output headers so that the file is downloaded rather than displayed
		//      // header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		//      // header( 'Content-Description: File Transfer' );
		//      // header( 'Content-type: application/csv' );
		//      // header( 'Content-Disposition: attachment; filename="MyDataFile.csv"' );
		//      // header( 'Expires: 0' );
		//      // header( 'Pragma: public' );
		// 	ob_start();
		// 	     // Set header row values
		//     $csv_fields=array();
		//     $csv_fields[] = 'Team';
		//     $csv_fields[] = 'Iteration';
		//     $csv_fields[] = 'Overall';
		//     $csv_fields[] = 'Access';
		//     $csv_fields[] = 'Sensitivity';
		//     $csv_fields[] = 'Trust';
		//     $csv_fields[] = 'Knowledge';
		//     $csv_fields[] = 'Commitment';
		//     $csv_fields[] = 'Collaboration';
		// 	// create a file pointer connected to the output stream
		// 	$output = @fopen('wp-content/uploads/export.csv', 'w') or show_error("Can't open php://output");
		// 	echo $output;
		// 	// The results for every team is already available
		// 	// in $this -> total_results
		// 	echo '<table>';
		// 	for ($i = 0; $i < count($csv_fields); $i++){
		// 		echo '<th>' . $csv_fields[$i] .'</th>';
		// 	};
		// 	$qual_inds_array = array();

		// 	echo '</table>';




		// 	// output the column headings
		// 	fputcsv($output, $csv_fields);

		// 	fputcsv($output, array("Text1", "Text2") ); 
		// 	fclose($output);
		// 	$csvStr = ob_get_contents(); // + "csv";
		// 	ob_end_clean();

		// 	echo $csvStr;
		// } 

	}
}
