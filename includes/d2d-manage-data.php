<?php
//  Implements RetrieveData class to retrieve data from indicators table
//  decode for real data (as "total" or "rostered"
//  display in table form and save as csv files as necessary.
//  The class is accessible from the "Fetch Data" class,
//  as a provider of data and as a receiver of data to be displayed and saved.)
//  
/**
 *
 *
 * @author Neil
 * @version 0.5
 * @created 11-January-2016
 */
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( !class_exists( 'D2D_manage_data' ) ) {
	class D2D_manage_data {


		public $team_results;
		public $weights;
		public $data_vars = array(); // Indicators from which quality scores are calculater
		public $data_table = array();
		public $quality_keys = array();
		public $quality_inds = array(); //Six plus one indicators of quality
		public $quality_table = array();

		public function __construct() {
			add_shortcode( 'manage data', array( $this, 'data_shortcode' ) );
		}

		public function build_tables(){
			$this -> retrieve_data();
			$this -> prepare_data_table_header();
			$this -> build_data_table();
			$this -> impute_data();
			$this -> prepare_quality_table_header();
			$this -> build_quality_table();
		}

		public function manage_data(){
			$this -> build_tables();

			$this -> display_data_table();
			
			$this -> display_quality_table();
			// $this -> exportQualityToCSV();
		}

		/**
		 * Retrieve individual team data from the indicators database table
		 * Retrieve the active table of weights and levels
		 * This provides the list of indicators used in the composite indicators
		 * and the composite indicators calculated.
		 * @return [array] All locked team results as an array and weights as array.
		 */
		public function retrieve_data(){

			$sql_team = 'SELECT * FROM indicators WHERE save_status = "locked" ';
			$sql_weights = 'SELECT * FROM weights_and_thresholds';

			global $wpdb;
			$this -> team_results = $wpdb -> get_results( $sql_team, ARRAY_A );
			$this -> weights = $wpdb -> get_results( $sql_weights, ARRAY_A );
		}

// Data table

		public function prepare_data_table_header(){
			// Retrieve compositie variables as first column of weights array
			// $this -> data_vars[] = 'team_code';
			$this -> data_vars[] = 'team_year';
			// $this -> data_vars[] = 'team_year';
			foreach($this -> weights as $row){
				array_push($this -> data_vars, $row['short_label']);
			}
		}
			
		public function build_data_table(){
			//  Iterate through the team results
			//  For each one, iterate through the data vars and place them in the data table
			foreach ($this -> team_results as $team){
				$team_row = array();
				foreach( $this -> data_vars as $var){
					if ( $var != 'team_year'){
						if ( is_null($team[$var])){
							$team_row[$var] = null;
						} else {
							$numeric_var = $this -> d2d_decode($team[$var]);
							$team_row[$var] = $numeric_var['total'];
						}
					}
					$team_row['team_year'] = $team['team_code'] . '_' .  str_replace('D2D ', '', $team['year_code']);
				}
				$this -> data_table[$team_row['team_year']] = $team_row;
			}
		}

		public function impute_data(){
			$data_table_keys = array_keys($this -> data_table);
			// print_r($data_table_keys);
			foreach($this -> data_vars as $var){
					if ( $var != 'team_year'){
					$valid_var_table = array();
					foreach( $data_table_keys as $key){
						$this_val = $this -> data_table[$key][$var];
						if(!($this_val == '')){
							$valid_var_table[] = $this_val;
						} else {
							 $this -> data_table[$key][$var]  = "n/a" ;
						}
					}
					foreach( $data_table_keys as $key){
						$this_val = $this -> data_table[$key][$var];
						if( ($this_val == 'n/a')  or
							($this_val == 'missing') ) {
								$imputed_key  = array_rand ( $valid_var_table);
								$this -> data_table[$key][$var] = $valid_var_table[$imputed_key];						;
						}
					}
				}
			}
		}

		public function display_data_table(){
			echo '<table>';
			foreach($this -> data_vars  as $ind){
				echo '<th>' . $ind .'</th>';
			};

			foreach($this -> data_table as $row){
				echo '<tr>';
				foreach($this -> data_vars as $var){
					echo '<td>' . $row[$var] . '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
		}
		
// Quality table
		public function prepare_quality_table_header(){
			// Retrievequality variables as a subset of the columns of the weights
			$q_keys = array_keys($this -> weights[0]);
			$offset = 4;
			for ($i = 0; $i < 7; $i++){
				$this -> quality_keys[$i] = $q_keys[$i + $offset];
			}

		}

		public function build_quality_table(){
			$team_codes = array_keys($this -> data_table);
			foreach ($team_codes as $code){
				// echo ' Type is ' . gettype($code) . '<br>';
				// print_r($code); 
				$team = $this -> data_table[$code];
				$team_row = array();
				$starfield_array = $this -> calculate_starfield ( $this -> quality_keys, $team);
				foreach( $this -> quality_keys as $var){
					$team_row[$var] = $starfield_array[$var];
				}
				$this -> quality_table[$code] = $team_row;
			}
		}

		public function display_quality_table(){
			echo '<table>';
			echo '<th>team_iteration</th>';
			foreach($this -> quality_keys  as $ind){
				echo '<th>' . $ind .'</th>';
			};

			$team_codes = array_keys($this -> quality_table);
			foreach($team_codes as $code){
				$team = $this -> quality_table[$code];
				echo '<tr>';
				echo '<td>' . $code . '</td>';
				foreach($this -> quality_keys  as $ind){
					echo '<td>' . $this -> quality_table[$code][$ind] . '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';
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
		 * Calculates the value of a single team's quality (Starfield) indicator
		 * in each of the seven domains listed.
		 * @param  [array] $quality_labels [A list of the names of each of the quality domains 
		 * as identified in the column names of the weights table]
		 * @param  [array] $team_data      [A list of values of all indicators associated with the selected team.
		 * This list includes imputed values (random replacement) for missing indicators for that team]
		 * @return [array] $starfield_array [List of six plus one values for team in each of the domainss]
		 */
		public function calculate_starfield( $quality_labels,  $team_data ){
			
			$starfield_array = array();
			$weights = $this -> weights;
			$domain_factor = array();
			$starfield_overall = 0;
	
			// Step through the domains
			foreach ( $quality_labels as $qual_name){
					// echo '<br>domain: '  . $qual_name . '<br>';
				
				if($qual_name != 'overall'){

					$composite_number = 0;
					// Step through the indicator weights for the domain and the team values for the indicator
					// $domain_factor[$qual_name] = 0;
					foreach ($weights as $w_row ){
						$domain_factor[$qual_name] += $w_row[$qual_name];
					}
					// echo '<br>domain factor:' . $domain_factor[$qual_name] . '<br>';
					
					foreach ($weights as $w_row ){
						// Retrieve the indicator name
						$data_name = $w_row['short_label'];
						// echo '<br>indicator:' . $data_name . '<br>';
						// Retrieve the team value for that indicator
						$temp_value0 = $team_data[$data_name];
						// echo 'raw value:' . $temp_value0 . '<br>';
						// Apply the threshold for this indicator
						$data_value = $this -> d2d_apply_threshold( $temp_value0, $w_row['lower'], $w_row['upper'] );
						// echo 'normalized value:' . $data_value . '<br>';
						// Apply the weight to the modified indicator value
						$temp_number = $data_value * $w_row[$qual_name];
						// echo 'weighted value:' . $temp_number . '<br>';
						// Add the weighted contribution of this indicator value to this domain for this team
						$composite_number += $temp_number;
					}
					// Format the calculated value
					$temp_score = number_format( $composite_number ,2 );
					$starfield_array[$qual_name] = number_format( $composite_number/$domain_factor[$qual_name] ,2 );
					$starfield_overall += $temp_score;
					// echo 'accumulated contribution:' . $starfield_array[$qual_name] . '<br>';
					// echo 'starfield_overall:' . $starfield_overall. '<br>';
				} else {
					$starfield_array['overall'] = $starfield_overall;
					// echo 'overall contribution:' . $starfield_array['overall'] . '<br>';
				}
			}
			// echo '<br>Starfield<br>';
			// Return the complete list
			// print_r($starfield_array);
			// echo '<br>';
			return $starfield_array;
		}

		/**
		 * Contrain the indicator value between supplied thresholds
		 * @param  [numeric] $val  [Vlaue to be constrained]
		 * @param  [numeric] $low  [Threshold below which values are to be constrained]
		 * @param  [numeric] $high [Threshold above which values are to be constrained]
		 * @return [numeric] $new_val  [Contstrained value]
		 */
		public function d2d_apply_threshold( $val, $low, $high ) {
			// Normal treatment: lower threshold is 0, higher is 1
			if($low < $high){
				// Below lower - unfavorable
				if ( $val <= $low ) {
				// Above upper - favorable
					$new_val = 0;
				} elseif ( $val >= $high ) {
				// In between
						$new_val = 100;
				} else {
					$new_val = 100 * ( $val - $low )/( $high - $low );
				}
			} else {
			// Exception treatment: lower threshold is 1, high is 0
				// Below lower - favorable
				if ( $val <= $low ) {
					$new_val = 100;
				}elseif ( $val >= $high ) {
				// Above upper - unfavorable
					$new_val = 0;
				} else {
					$new_val = 100 * ( $low - $val )/( $high - $low );
				}
			}
			return $new_val;
		}
		
		public function get_quality_indicator($indicator_labels,  $team, $iteration){
			$code = $team . '_' .  str_replace('D2D ', '', $iteration);
			$return_array = array();
			foreach ( $indicator_labels as $ind ) {
				$datum = $this -> quality_table[$code][$ind];
				$return_array[ $ind ] = array(
					'total' => $datum,
					'rostered' => 234
				);
			}
			return $return_array;
		}

		public function write_to_db(){
			$team_keys = array_keys($this -> data_table);
			
			global $wpdb;

			$truncate_sql = "TRUNCATE TABLE quality_results";

			// $wpdb -> query($truncate_sql);

			$names = array();
			$values = array();
			$format = array();

			foreach($team_keys as $key){
			// $key = $team_keys[0];

				$sql_array = array();
				$keys = array_keys($this -> data_table[$key]);
				foreach($keys as $k){
					$sql_array[$k] = $this -> data_table[$key][$k];
				}
				$keys = array_keys($this -> quality_table[$key]);
				foreach($keys as $k){
					$sql_array[$k] = $this -> quality_table[$key][$k];
				}

				//build array of formats
				$format = array();
				$format[] = '%s';
				for ($i=0; $i <  count($sql_array) - 1 ; $i++) { 
					$format[] = '%f';
				}
				//save to the table
				$wpdb -> insert(
				'quality_results',
					$sql_array,
					$format
					);
			}
			// echo '<br>names<br>';
			// print_r($sql_array);
			// echo '<br>format<br>';
			// print_r($format);
		}
		
		// Create the shortcode for this plugin
		public function data_shortcode() {
			ob_start();
			$this -> manage_data();
			$this -> write_to_db();
			return ob_get_clean();
		}

	}
}




if ( class_exists( 'D2D_manage_data' ) ) {
	$D2D_manage_data = new D2D_manage_data();
}